<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Category;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\User;

class GeminiChatController extends Controller
{
    private $apiKey;
    private const MAX_TOOL_ITERATIONS = 5;
    private const MAX_HISTORY_TURNS = 10;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * Main chat endpoint — handles messages, tool loops, and session history.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $userMessage = $request->input('message');

        // ── Build context-aware system prompt ──────────────────────────
        $systemInstruction = $this->buildSystemPrompt();

        // ── Tool declarations ─────────────────────────────────────────
        $toolsConfig = $this->getToolsConfig();

        // ── Load conversation history from session ────────────────────
        $history = session('chatbot_history', []);

        // Add the new user message to history
        $history[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        try {
            // ── Tool call loop (up to MAX_TOOL_ITERATIONS) ────────────
            $iterations = 0;
            $contents = $history;

            while ($iterations < self::MAX_TOOL_ITERATIONS) {
                $iterations++;

                // Ensure functionCall args and functionResponse responses are JSON objects
                $cleanedContents = $this->cleanContentsForApi($contents);

                $response = Http::connectTimeout(30)
                    ->timeout(30)
                    ->withOptions([
                        'force_ip_resolve' => 'v4'
                    ])
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}", [
                        'contents' => $cleanedContents,
                        'tools' => $toolsConfig,
                        'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                    ]);

                if (!$response->successful()) {
                    $status = $response->status();
                    $body = $response->body();
                    \Log::error("Gemini API Error: {$status} - {$body}");

                    if ($status === 429) {
                        return $this->errorResponse(
                            "⚠️ **Gemini API Quota Exceeded (429)**\n\n" .
                            "The configured Gemini API key has exceeded its daily free quota or rate limit.\n\n" .
                            "**To resolve this:**\n" .
                            "1. Generate a new API key from [Google AI Studio](https://aistudio.google.com/).\n" .
                            "2. Update the `GEMINI_API_KEY` value in your project's `.env` file:\n" .
                            "   ```env\n" .
                            "   GEMINI_API_KEY=your_new_api_key_here\n" .
                            "   ```\n" .
                            "3. Restart your Laravel server if cached."
                        );
                    }

                    if ($status === 403) {
                        return $this->errorResponse(
                            "🔒 **Gemini API Permission Denied (403)**\n\n" .
                            "The configured API key appears to be invalid or restricted.\n\n" .
                            "**To resolve this:**\n" .
                            "1. Confirm your key at [Google AI Studio](https://aistudio.google.com/).\n" .
                            "2. Update the `GEMINI_API_KEY` value in your project's `.env` file."
                        );
                    }

                    return $this->errorResponse("Unable to contact AI service. (Error {$status}: " . substr($body, 0, 100) . "...)");
                }

                $result = $response->json();
                $candidate = $result['candidates'][0]['content'] ?? null;

                if (!$candidate) {
                    return $this->errorResponse('Received an empty response from the AI.');
                }

                $firstPart = $candidate['parts'][0] ?? null;

                // ── If Gemini wants to call a function tool ───────────
                if (isset($firstPart['functionCall'])) {
                    $functionName = $firstPart['functionCall']['name'];
                    $args = $firstPart['functionCall']['args'] ?? [];

                    // Execute the tool
                    $toolResult = $this->executeTool($functionName, $args);

                    // Ensure functionCall args is encoded as a JSON object, not a list
                    $formattedFunctionCall = [
                        'name' => $functionName,
                        'args' => (object)$args
                    ];

                    // Append the model's function call and our response to the conversation
                    $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $formattedFunctionCall]]];
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [[
                            'functionResponse' => [
                                'name' => $functionName,
                                'response' => (object)['output' => json_encode($toolResult)]
                            ]
                        ]]
                    ];

                    // Continue the loop — Gemini may want to call another tool
                    continue;
                }

                // ── Gemini responded with text — we're done ───────────
                $rawText = $firstPart['text'] ?? 'How can I assist you today?';

                // Parse buttons from the response if Gemini included them
                $parsed = $this->parseButtonsFromResponse($rawText);

                // Save conversation to session (keep last N turns)
                $contents[] = ['role' => 'model', 'parts' => [['text' => $parsed['text']]]];
                $contents = array_slice($contents, -(self::MAX_HISTORY_TURNS * 2));
                session(['chatbot_history' => $contents]);

                return response()->json([
                    'status' => 'success',
                    'reply' => [
                        'text' => $parsed['text'],
                        'buttons' => $parsed['buttons']
                    ]
                ]);
            }

            // If we exhausted tool iterations, return what we have
            return $this->errorResponse('The request required too many lookups. Please try a simpler question.');

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Clear chat history.
     */
    public function clearHistory()
    {
        session()->forget('chatbot_history');
        return response()->json(['status' => 'success', 'message' => 'Chat history cleared.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // SYSTEM PROMPT
    // ══════════════════════════════════════════════════════════════════

    private function buildSystemPrompt(): string
    {
        $now = now()->format('l, F j, Y');
        $user = Auth::user();

        if ($user) {
            $role = ucfirst($user->role);
            $userContext = "The user is LOGGED IN as a **{$role}**.\n";
            $userContext .= "- **Name**: {$user->name}\n";
            $userContext .= "- **Email**: {$user->email}\n";
            $userContext .= "- **User ID**: {$user->id}\n";
            $userContext .= "- **Account Status**: {$user->status}\n";

            // Role-specific detailed context & instructions
            switch ($user->role) {
                case 'student':
                    $student = $user->student;
                    $enrollmentCount = Enrollment::where('user_id', $user->id)->count();
                    $lastEnrollment = Enrollment::where('user_id', $user->id)->with('course')->latest()->first();
                    $lastCourseTitle = $lastEnrollment?->course?->title ?? 'None';
                    
                    $userContext .= "- **Qualification**: " . ($student?->current_qualification ?? 'N/A') . "\n";
                    $userContext .= "- **Phone**: " . ($student?->phone ?? 'N/A') . "\n";
                    $userContext .= "- **Total Enrollments**: {$enrollmentCount}\n";
                    $userContext .= "- **Latest Enrollment**: {$lastCourseTitle} (Status: " . ($lastEnrollment?->status ?? 'N/A') . ")\n";
                    $userContext .= "ROLE GUIDELINE: Assist the student with course discovery, enrollment status, payments, mentor chat access, and certificate downloads. When discussing their enrollments or last enrollment, ALWAYS add a button to view their enrollments page (/student/my-enrollments).";
                    break;

                case 'firm':
                    $firm = $user->firm;
                    $bookingCount = Enrollment::where('user_id', $user->id)->where('type', 'firm')->count();
                    $lastBooking = Enrollment::where('user_id', $user->id)->where('type', 'firm')->with('course')->latest()->first();

                    $userContext .= "- **Organization**: " . ($firm?->org_name ?? $user->name) . "\n";
                    $userContext .= "- **Org Type**: " . ($firm?->org_type ?? 'Corporate/NGO') . "\n";
                    $userContext .= "- **Contact Person**: " . ($firm?->contact_person ?? $user->name) . "\n";
                    $userContext .= "- **Total Group Bookings**: {$bookingCount}\n";
                    $userContext .= "- **Latest Booking**: " . ($lastBooking?->course?->title ?? 'None') . " (Status: " . ($lastBooking?->status ?? 'N/A') . ")\n";
                    $userContext .= "ROLE GUIDELINE: Assist the firm with corporate offline course bookings, group cohort management, proposed venue/schedule status, payments, and multi-page QR certificate bundles. When discussing bookings, ALWAYS add a button to view corporate bookings (/firm/bookings).";
                    break;

                case 'college':
                    $college = $user->college;
                    $collegeId = $college?->id;
                    $courseCount = $collegeId ? Course::where('college_id', $collegeId)->count() : 0;
                    $pendingCount = $collegeId ? Enrollment::whereHas('course', fn($q) => $q->where('college_id', $collegeId))->where('status', 'pending')->count() : 0;

                    $userContext .= "- **Institution**: " . ($college?->institution_name ?? $user->name) . "\n";
                    $userContext .= "- **Campus Address**: " . ($college?->address ?? 'N/A') . "\n";
                    $userContext .= "- **Listed Offline Courses**: {$courseCount}\n";
                    $userContext .= "- **Pending Applications Awaiting Review**: {$pendingCount}\n";
                    $userContext .= "ROLE GUIDELINE: Assist the college with course listings, offline seat management, assigning mentors, approving/rejecting enrollment requests, and signature-authorized certificate issuance. Add buttons to management pages (/college/courses, /college/enrollments).";
                    break;

                case 'mentor':
                    $mentor = $user->mentor;
                    $assignedCoursesCount = $mentor ? Course::where('mentor_id', $mentor->id)->count() : 0;

                    $userContext .= "- **Expertise**: " . ($mentor?->expertise ?? 'N/A') . "\n";
                    $userContext .= "- **Qualification**: " . ($mentor?->qualification ?? 'N/A') . "\n";
                    $userContext .= "- **Assigned Offline Courses**: {$assignedCoursesCount}\n";
                    $userContext .= "ROLE GUIDELINE: Assist the mentor with viewing assigned courses, managing student live chat requests, and mentorship reports. Add buttons to mentor pages (/mentor/mycourses, /mentor/chat-requests).";
                    break;

                case 'admin':
                    $pendingColleges = User::where('role', 'college')->where('status', 'pending')->count();
                    $pendingFirms = User::where('role', 'firm')->where('status', 'pending')->count();

                    $userContext .= "- **System Role**: Platform Super Admin\n";
                    $userContext .= "- **Pending Institutional Approvals**: Colleges ({$pendingColleges}), Firms ({$pendingFirms})\n";
                    $userContext .= "ROLE GUIDELINE: Provide executive summaries of platform statistics, institution verification queues, course moderation, category oversight, and system analytics. Add buttons to admin pages (/admin/colleges, /admin/firms, /admin/courses, /admin/reports).";
                    break;

                default:
                    $userContext .= "ROLE GUIDELINE: Assist user based on platform guidelines.";
                    break;
            }
        } else {
            $userContext = "The user is a **Guest** (not logged in).\n" .
                "If they ask about their personal profile, enrollment history, certificates, or booking status, politely inform them that they need to sign in first, and append buttons for Login (/login) and Register (/register).\n" .
                "They can freely ask about available offline courses, colleges (including locations like Angamaly), course fees, and platform information.";
        }

        $prompt = <<<PROMPT
You are **EduConnect AI**, the official intelligent virtual assistant for **EduConnect** — Multi-Institutional Offline Course Enrollment & Mentorship Platform.
Today's date: {$now}.

{$userContext}

## Platform Context & Architecture
EduConnect connects Colleges with Students and Organizations (Firms) for offline skill development and professional coaching:
1. **Course Marketplace**: Offline training programs provided by accredited colleges with campus venues, offline schedules, price, seat capacity, and category.
2. **Dual Enrollment**:
   - **Students**: Individual enrollments with fixed campus venues and schedules.
   - **Firms**: Corporate group bookings where firms can propose their own venue and session schedule.
3. **Direct Mentorship**: Live chat guidance connecting enrolled learners with college-assigned mentors.
4. **QR-Verified Certification**: Colleges issue certificates with authorized signature uploads, downloadable as PDF with instant public QR verification.

## Your Capabilities
- You can query live data using your tools: search courses by keyword/location (e.g. Angamaly), get last enrollment details, list categories, inspect college info, retrieve enrollment statuses, check certificates, and view platform stats.
- Answer general and platform-specific questions concisely, clearly, and politely.

## Query Handling Instructions
1. **Enrollment Queries (e.g., "my last enrollment details", "enrollment status")**:
   - Call `get_last_enrollment` (or `get_user_enrollments`).
   - Detail the course title, college, venue, schedule, status (Pending/Confirmed/Rejected), payment status, fee amount, and any college notes.
   - **ALWAYS** include a redirect button to open the enrollment page:
     - For Students: `/student/my-enrollments`
     - For Firms: `/firm/bookings`
     - For Guests: `/login`

2. **Location & Course Inquiries (e.g., "new courses provided by colleges in Angamaly", "courses near Kochi")**:
   - Call `search_courses` with `location` or `query` set to the city/location name (e.g., "Angamaly").
   - Display title, college name, location/venue, dates, price, seat availability, and certification status.
   - **ALWAYS** append a button to Explore Courses (`/explore`) or the specific course page (`/courses/{slug}`).

3. **Certificate Inquiries**:
   - Call `get_certificate_status` or `get_last_enrollment`.
   - Explain whether the certificate has been issued by the college.
   - Include redirect button to `/student/my-enrollments` (Student) or `/firm/bookings` (Firm) or `/verify` (Public Verification).

4. **Mentor / Live Chat Queries**:
   - Direct students to `/student/chat` and mentors to `/mentor/chat-requests`.

## Response Formatting Rules
1. Always use standard **Markdown**: bold labels (`**Course**: Title`), bullet lists, and tables/headings where helpful.
2. Keep responses structured, concise, and easy to read.
3. Use emojis effectively (📚 🎓 📅 💰 📍 ✅ ⏳ 📜 💬).

## Button Output Format
Whenever your response references a specific page or next action, ALWAYS append a JSON block at the VERY END of your response text in this EXACT format:

:::buttons
[
  {"action": "redirect", "label": "Button Text", "url": "/path/to/page"},
  {"action": "chat_suggest", "label": "Button Text", "text": "Follow-up query to ask"}
]
:::

### URL Reference Guide for Buttons
- Student Enrollments: `/student/my-enrollments`
- Student Explore: `/explore`
- Student Chat with Mentor: `/student/chat`
- Firm Group Bookings: `/firm/bookings`
- Firm Explore: `/firm/explore`
- Firm Groups/Cohorts: `/firm/groups`
- College Course Management: `/college/courses`
- College Enrollment Approvals: `/college/enrollments`
- College Mentors: `/college/mentors`
- College Certificate Issuance: `/college/certificates`
- Mentor Assigned Courses: `/mentor/mycourses`
- Mentor Chat Requests: `/mentor/chat-requests`
- Admin Approvals: `/admin/colleges` or `/admin/firms`
- Admin Reports: `/admin/reports`
- Public Verification: `/verify`
- Login / Register: `/login`, `/register`

Include 1-4 relevant buttons maximum. Ensure labels are clear and include icons/emojis (e.g., "📋 Open My Enrollments", "🔍 Explore All Courses").
PROMPT;
        return $prompt;
    }

    // ══════════════════════════════════════════════════════════════════
    // TOOL DECLARATIONS
    // ══════════════════════════════════════════════════════════════════

    private function getToolsConfig(): array
    {
        return [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'get_last_enrollment',
                        'description' => 'Fetch full details of the current user\'s single most recent course enrollment or group booking. Use this when the user asks "my last enrollment details", "latest enrollment", or "what course did I enroll in last".',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'search_courses',
                        'description' => 'Search offline courses by keyword or location (e.g., "Angamaly", "Python", "Web"). Matches title, description, venue, category, and college name/address.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Keyword to search in course title, description, or category (e.g., "web development").'],
                                'location' => ['type' => 'STRING', 'description' => 'City or location to filter by (e.g., "Angamaly", "Kochi", "Ernakulam").'],
                                'sort_by' => ['type' => 'STRING', 'description' => 'Sort order: "latest" for new courses, "price_low", "price_high". Default is "latest".']
                            ],
                            'required' => []
                        ]
                    ],
                    [
                        'name' => 'get_course_details',
                        'description' => 'Get full details of a specific course by its title or slug. Use this when the user asks about a particular course.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'identifier' => ['type' => 'STRING', 'description' => 'The course title or slug to look up.']
                            ],
                            'required' => ['identifier']
                        ]
                    ],
                    [
                        'name' => 'get_all_categories',
                        'description' => 'List all available course categories with the number of courses in each. No parameters needed.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_courses_by_category',
                        'description' => 'List courses filtered by a specific category name.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'category' => ['type' => 'STRING', 'description' => 'The category name to filter by (e.g., "Programming", "Design").']
                            ],
                            'required' => ['category']
                        ]
                    ],
                    [
                        'name' => 'get_college_info',
                        'description' => 'Get information about colleges on the platform, including location, address, contact details, and courses count. Can search by college name or location (e.g., "Angamaly").',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Optional college name or location (e.g. "Angamaly") to search for.']
                            ],
                            'required' => []
                        ]
                    ],
                    [
                        'name' => 'get_user_profile',
                        'description' => 'Retrieve the profile information of the currently authenticated user including their role-specific details.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_user_enrollments',
                        'description' => 'Fetch the list of courses the current user has enrolled in or booked, including status and payment info.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_enrollment_status',
                        'description' => 'Check the status and details of a specific enrollment by course title.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'course_title' => ['type' => 'STRING', 'description' => 'The title of the course to check enrollment status for.']
                            ],
                            'required' => ['course_title']
                        ]
                    ],
                    [
                        'name' => 'get_certificate_status',
                        'description' => 'Check if a certificate has been issued for a user\'s enrollment.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'course_title' => ['type' => 'STRING', 'description' => 'The course title to check certificate status for.']
                            ],
                            'required' => []
                        ]
                    ],
                    [
                        'name' => 'get_college_pending_enrollments',
                        'description' => 'For College users: List student and firm enrollment requests awaiting review/approval.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_firm_group_bookings',
                        'description' => 'For Firm users: List bulk group bookings, participant rosters, and schedule proposals.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_mentor_assigned_courses',
                        'description' => 'For Mentor users: List courses assigned by colleges and pending student live chat requests.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_admin_pending_approvals',
                        'description' => 'For Admin users: List colleges and firms currently waiting for platform verification and approval.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                    [
                        'name' => 'get_platform_stats',
                        'description' => 'Get overall platform statistics: total courses, colleges, categories, mentors, and students.',
                        'parameters' => ['type' => 'OBJECT', 'properties' => (object)[], 'required' => []]
                    ],
                ]
            ]
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    // TOOL EXECUTION
    // ══════════════════════════════════════════════════════════════════

    private function executeTool(string $name, array $args): array
    {
        return match ($name) {
            'get_last_enrollment'            => $this->toolGetLastEnrollment(),
            'search_courses'                 => $this->toolSearchCourses($args),
            'get_course_details'             => $this->toolGetCourseDetails($args),
            'get_all_categories'             => $this->toolGetAllCategories(),
            'get_courses_by_category'        => $this->toolGetCoursesByCategory($args),
            'get_college_info'               => $this->toolGetCollegeInfo($args),
            'get_user_profile'               => $this->toolGetUserProfile(),
            'get_user_enrollments'           => $this->toolGetUserEnrollments(),
            'get_enrollment_status'          => $this->toolGetEnrollmentStatus($args),
            'get_certificate_status'         => $this->toolGetCertificateStatus($args),
            'get_college_pending_enrollments'=> $this->toolGetCollegePendingEnrollments(),
            'get_firm_group_bookings'        => $this->toolGetFirmGroupBookings(),
            'get_mentor_assigned_courses'    => $this->toolGetMentorAssignedCourses(),
            'get_admin_pending_approvals'    => $this->toolGetAdminPendingApprovals(),
            'get_platform_stats'             => $this->toolGetPlatformStats(),
            default                          => ['error' => "Unknown tool: {$name}"]
        };
    }

    // ── Individual Tool Implementations ───────────────────────────────

    private function toolGetLastEnrollment(): array
    {
        if (!Auth::check()) {
            return ['error' => 'User is not logged in. Please ask them to sign in first.'];
        }

        $enrollment = Enrollment::where('user_id', Auth::id())
            ->with(['course.college', 'course.category', 'course.mentor.user', 'participants'])
            ->latest()
            ->first();

        if (!$enrollment) {
            return ['message' => 'You do not have any enrollments or bookings yet.'];
        }

        $c = $enrollment->course;

        return [
            'enrollment_id' => $enrollment->id,
            'type' => $enrollment->type,
            'course_title' => $c?->title ?? 'Unknown Course',
            'course_slug' => $c?->slug ?? '',
            'college_name' => $c?->college?->institution_name ?? 'N/A',
            'college_address' => $c?->college?->address ?? 'N/A',
            'category' => $c?->category?->name ?? 'N/A',
            'venue' => $c?->venue ?? $enrollment->requested_venue ?? 'TBA',
            'start_date' => $c?->start_date ?? $enrollment->proposed_start ?? 'TBA',
            'end_date' => $c?->end_date ?? $enrollment->proposed_end ?? 'TBA',
            'time_slot' => $c?->time_slot ?? $enrollment->proposed_time ?? 'TBA',
            'status' => $enrollment->status,
            'payment_status' => $enrollment->payment_status,
            'total_amount' => $enrollment->total_amount ? ('₹' . number_format($enrollment->total_amount, 2)) : (($c && $c->price) ? ('₹' . number_format($c->price, 2)) : 'Free'),
            'college_note' => $enrollment->college_note ?? 'No extra notes',
            'certificate_issued' => $enrollment->certificate_issued ? 'Yes' : 'No',
            'certificate_code' => $enrollment->certificate_code ?? 'Not yet issued',
            'mentor_name' => $c?->mentor?->user?->name ?? 'Not assigned yet',
            'participant_count' => $enrollment->type === 'firm' ? ($enrollment->participant_count ?? count($enrollment->participants)) : 1,
            'enrolled_at' => $enrollment->created_at?->format('F j, Y, g:i a'),
        ];
    }

    private function toolSearchCourses(array $args): array
    {
        $query = isset($args['query']) ? $args['query'] : '';
        $location = isset($args['location']) ? $args['location'] : '';
        $sortBy = isset($args['sort_by']) ? $args['sort_by'] : 'latest';

        $qBuilder = Course::where('status', 'active')
            ->with(['category:id,name', 'college:id,institution_name,address']);

        // Filter by location if specified
        if (!empty($location)) {
            $qBuilder->where(function ($q) use ($location) {
                $q->where('venue', 'like', "%{$location}%")
                  ->orWhereHas('college', fn($c) => $c->where('address', 'like', "%{$location}%")
                                                     ->orWhere('institution_name', 'like', "%{$location}%"));
            });
        }

        // Filter by keyword query if specified
        if (!empty($query)) {
            $qBuilder->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('venue', 'like', "%{$query}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$query}%"))
                  ->orWhereHas('college', fn($c) => $c->where('institution_name', 'like', "%{$query}%")
                                                     ->orWhere('address', 'like', "%{$query}%"));
            });
        }

        // Sorting
        if ($sortBy === 'price_low') {
            $qBuilder->orderBy('price', 'asc');
        } elseif ($sortBy === 'price_high') {
            $qBuilder->orderBy('price', 'desc');
        } else {
            $qBuilder->latest();
        }

        $courses = $qBuilder->take(6)
            ->get()
            ->map(fn($c) => [
                'title' => $c->title,
                'slug' => $c->slug,
                'category' => $c->category?->name ?? 'N/A',
                'college' => $c->college?->institution_name ?? 'N/A',
                'college_address' => $c->college?->address ?? 'N/A',
                'type' => $c->course_type,
                'price' => $c->price > 0 ? '₹' . number_format($c->price, 2) : 'Free',
                'venue' => $c->venue ?? 'TBA',
                'start_date' => $c->start_date,
                'end_date' => $c->end_date,
                'available_seats' => $c->available_seats,
                'total_seats' => $c->total_seats,
                'is_certified' => $c->is_certified ? 'Yes' : 'No',
                'created_at' => $c->created_at?->format('M j, Y'),
            ])
            ->toArray();

        $searchTerm = trim($query . ' ' . $location);
        return $courses ?: ['message' => 'No active courses found matching "' . ($searchTerm ?: 'your query') . '".'];
    }

    private function toolGetCourseDetails(array $args): array
    {
        $identifier = $args['identifier'] ?? '';
        $course = Course::where('slug', 'like', "%{$identifier}%")
            ->orWhere('title', 'like', "%{$identifier}%")
            ->with(['category:id,name', 'college:id,institution_name,address', 'mentor:id,qualification,expertise', 'mentor.user:id,name'])
            ->first();

        if (!$course) {
            return ['error' => 'Course not found with identifier: ' . $identifier];
        }

        return [
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'category' => $course->category?->name ?? 'N/A',
            'college' => $course->college?->institution_name ?? 'N/A',
            'college_address' => $course->college?->address ?? 'N/A',
            'mentor' => $course->mentor?->user?->name ?? 'Not assigned',
            'mentor_expertise' => $course->mentor?->expertise ?? 'N/A',
            'type' => $course->course_type,
            'price' => $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free',
            'venue' => $course->venue ?? 'TBA',
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'time_slot' => $course->time_slot ?? 'TBA',
            'available_seats' => $course->available_seats,
            'total_seats' => $course->total_seats,
            'is_certified' => $course->is_certified ? 'Yes' : 'No',
            'status' => $course->status,
        ];
    }

    private function toolGetAllCategories(): array
    {
        return Category::withCount('course')
            ->get()
            ->map(fn($c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'course_count' => $c->course_count,
            ])
            ->toArray();
    }

    // ══════════════════════════════════════════════════════════════════
    // RESPONSE PARSING
    // ══════════════════════════════════════════════════════════════════

    /**
     * Extract :::buttons [...] ::: JSON from the AI response text.
     */
    private function parseButtonsFromResponse(string $text): array
    {
        $buttons = [];

        // Match the :::buttons ... ::: block
        if (preg_match('/:::buttons\s*\n?\s*(\[[\s\S]*?\])\s*\n?\s*:::/i', $text, $matches)) {
            $jsonStr = $matches[1];
            $decoded = json_decode($jsonStr, true);

            if (is_array($decoded)) {
                foreach ($decoded as $btn) {
                    if (isset($btn['action'], $btn['label'])) {
                        $button = [
                            'action' => $btn['action'],
                            'label' => $btn['label'],
                        ];
                        if ($btn['action'] === 'redirect' && isset($btn['url'])) {
                            $button['url'] = $btn['url'];
                        } elseif ($btn['action'] === 'chat_suggest' && isset($btn['text'])) {
                            $button['text'] = $btn['text'];
                        }
                        $buttons[] = $button;
                    }
                }
            }

            // Remove the buttons block from the visible text
            $text = trim(preg_replace('/:::buttons\s*\n?\s*\[[\s\S]*?\]\s*\n?\s*:::/i', '', $text));
        }

        return [
            'text' => $text,
            'buttons' => $buttons,
        ];
    }

    /**
     * Clean conversation history contents to ensure proper JSON serialization
     * of empty functionCall args and functionResponse responses.
     */
    private function cleanContentsForApi(array $contents): array
    {
        foreach ($contents as &$content) {
            if (isset($content['parts']) && is_array($content['parts'])) {
                foreach ($content['parts'] as &$part) {
                    // Ensure functionCall args is always a JSON object, never an array
                    if (isset($part['functionCall'])) {
                        $args = $part['functionCall']['args'] ?? [];
                        $part['functionCall']['args'] = is_array($args) && empty($args)
                            ? new \stdClass()
                            : (object)$args;
                    }
                    // Ensure functionResponse response is always a JSON object
                    if (isset($part['functionResponse'])) {
                        $resp = $part['functionResponse']['response'] ?? [];
                        if (is_array($resp) && !empty($resp)) {
                            // Encode nested tool result as a string to avoid schema issues
                            $part['functionResponse']['response'] = (object)$resp;
                        } else {
                            $part['functionResponse']['response'] = new \stdClass();
                        }
                    }
                }
            }
        }
        return $contents;
    }

    /**
     * Standard error response shape.
     */
    private function errorResponse(string $message)
    {
        return response()->json([
            'status' => 'error',
            'reply' => [
                'text' => $message,
                'buttons' => []
            ]
        ]);
    }
}