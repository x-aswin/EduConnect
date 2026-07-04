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
            $userContext = "The user is LOGGED IN as a **{$role}**. Name: {$user->name}, Email: {$user->email}, ID: {$user->id}.";

            // Add role-specific context
            if ($user->role === 'student') {
                $enrollmentCount = Enrollment::where('user_id', $user->id)->count();
                $userContext .= " They have {$enrollmentCount} enrollment(s).";
            } elseif ($user->role === 'firm') {
                $bookingCount = Enrollment::where('user_id', $user->id)->where('type', 'firm')->count();
                $userContext .= " They have {$bookingCount} firm booking(s).";
            }
        } else {
            $userContext = "The user is a **Guest** (not logged in). If they ask for profile data, enrollments, or certificates, politely tell them to sign in first and provide a sign-in button.";
        }

        $prompt = <<<PROMPT
You are **EduConnect AI**, the intelligent assistant for the EduConnect education platform.
Today's date: {$now}.
{$userContext}

## Your Capabilities
- You can search courses, categories, colleges, mentors, and enrollments from the live database using your tools.
- You can answer general knowledge questions about any topic.
- You are helpful, friendly, and concise.

## Response Formatting Rules
1. Always use **Markdown** formatting: bold, bullet lists, numbered lists, headings (use ### at most).
2. Keep responses concise but informative. Use bullet points for lists.
3. When showing course information, always include: title, price, venue, dates, available seats.
4. Use emojis sparingly for visual appeal (📚 🎓 📅 💰 📍 ✅ ❌ ⏳).

## Button Rules
When your response can lead to a useful next action, append a JSON block at the very end of your text response in this exact format:

:::buttons
[
  {"action": "redirect", "label": "Button Text", "url": "/path/to/page"},
  {"action": "chat_suggest", "label": "Button Text", "text": "Follow-up question to ask"}
]
:::

Button guidelines:
- Use "redirect" for navigation: viewing courses (/courses/{slug}), explore page (/explore), enrollments (/student/my-enrollments), dashboard (/dashboard), login (/login), register (/register).
- Use "chat_suggest" for follow-up questions the user might want to ask.
- Include 1-4 buttons maximum. Don't add buttons for simple greetings.
- Always use the correct URL paths for this platform.

## URL Reference
- Course detail: /courses/{slug}
- Explore courses: /explore
- Student enrollments: /student/my-enrollments
- Student dashboard: /student/dashboard
- Firm bookings: /firm/bookings
- Firm dashboard: /firm/dashboard
- Login: /login
- Register: /register

## Tool Usage
- If the user asks about courses, categories, colleges, or enrollments — ALWAYS use the appropriate tool first.
- If no matching tool exists, answer from your general knowledge.
- If a tool requires authentication and the user is a guest, explain they need to sign in.
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
                        'name' => 'search_courses',
                        'description' => 'Search courses by keyword. Matches against title, category name, venue, and description. Returns up to 6 results.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Search keyword (e.g., "python", "web development", "online").']
                            ],
                            'required' => ['query']
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
                        'description' => 'Get information about colleges on the platform. Can search by name or list all.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Optional college name to search for. Leave empty to list all.']
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
                            'required' => ['course_title']
                        ]
                    ],
                    [
                        'name' => 'get_platform_stats',
                        'description' => 'Get overall platform statistics: total courses, colleges, categories, mentors, and students. Use this for "tell me about EduConnect" type questions.',
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
            'search_courses'        => $this->toolSearchCourses($args),
            'get_course_details'    => $this->toolGetCourseDetails($args),
            'get_all_categories'    => $this->toolGetAllCategories(),
            'get_courses_by_category' => $this->toolGetCoursesByCategory($args),
            'get_college_info'      => $this->toolGetCollegeInfo($args),
            'get_user_profile'      => $this->toolGetUserProfile(),
            'get_user_enrollments'  => $this->toolGetUserEnrollments(),
            'get_enrollment_status' => $this->toolGetEnrollmentStatus($args),
            'get_certificate_status' => $this->toolGetCertificateStatus($args),
            'get_platform_stats'    => $this->toolGetPlatformStats(),
            default                 => ['error' => "Unknown tool: {$name}"]
        };
    }

    // ── Individual Tool Implementations ───────────────────────────────

    private function toolSearchCourses(array $args): array
    {
        $query = $args['query'] ?? '';
        $courses = Course::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('venue', 'like', "%{$query}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$query}%"));
            })
            ->with('category:id,name', 'college:id,institution_name')
            ->take(6)
            ->get()
            ->map(fn($c) => [
                'title' => $c->title,
                'slug' => $c->slug,
                'category' => $c->category?->name ?? 'N/A',
                'college' => $c->college?->institution_name ?? 'N/A',
                'type' => $c->course_type,
                'price' => $c->price > 0 ? '₹' . number_format($c->price, 2) : 'Free',
                'venue' => $c->venue ?? 'TBA',
                'start_date' => $c->start_date,
                'end_date' => $c->end_date,
                'available_seats' => $c->available_seats,
                'total_seats' => $c->total_seats,
                'is_certified' => $c->is_certified ? 'Yes' : 'No',
            ])
            ->toArray();

        return $courses ?: ['message' => 'No courses found matching "' . $query . '".'];
    }

    private function toolGetCourseDetails(array $args): array
    {
        $identifier = $args['identifier'] ?? '';
        $course = Course::where('slug', 'like', "%{$identifier}%")
            ->orWhere('title', 'like', "%{$identifier}%")
            ->with('category:id,name', 'college:id,institution_name', 'mentor:id,qualification,expertise', 'mentor.user:id,name')
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

    private function toolGetCoursesByCategory(array $args): array
    {
        $categoryName = $args['category'] ?? '';
        $category = Category::where('name', 'like', "%{$categoryName}%")->first();

        if (!$category) {
            return ['error' => 'Category not found: ' . $categoryName];
        }

        $courses = Course::where('category_id', $category->id)
            ->where('status', 'active')
            ->with('college:id,institution_name')
            ->take(8)
            ->get()
            ->map(fn($c) => [
                'title' => $c->title,
                'slug' => $c->slug,
                'college' => $c->college?->institution_name ?? 'N/A',
                'price' => $c->price > 0 ? '₹' . number_format($c->price, 2) : 'Free',
                'venue' => $c->venue ?? 'TBA',
                'start_date' => $c->start_date,
                'available_seats' => $c->available_seats,
            ])
            ->toArray();

        return $courses ?: ['message' => 'No active courses found in the "' . $category->name . '" category.'];
    }

    private function toolGetCollegeInfo(array $args): array
    {
        $query = $args['query'] ?? '';

        $colleges = College::when($query, function ($q) use ($query) {
                $q->where('institution_name', 'like', "%{$query}%");
            })
            ->with('user:id,name,email')
            ->withCount('courses')
            ->take(5)
            ->get()
            ->map(fn($c) => [
                'institution_name' => $c->institution_name,
                'contact_person' => $c->contact_person ?? $c->user?->name ?? 'N/A',
                'address' => $c->address ?? 'N/A',
                'website' => $c->website ?? 'N/A',
                'phone' => $c->college_phone ?? 'N/A',
                'total_courses' => $c->courses_count,
            ])
            ->toArray();

        return $colleges ?: ['message' => 'No colleges found' . ($query ? ' matching "' . $query . '"' : '') . '.'];
    }

    private function toolGetUserProfile(): array
    {
        if (!Auth::check()) {
            return ['error' => 'User is not logged in. Please ask them to sign in first.'];
        }

        $user = Auth::user();
        $profile = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'member_since' => $user->created_at?->format('F j, Y'),
        ];

        // Add role-specific data
        if ($user->role === 'student' && $user->student) {
            $profile['phone'] = $user->student->phone ?? 'Not set';
            $profile['qualification'] = $user->student->current_qualification ?? 'Not set';
            $profile['gender'] = $user->student->gender ?? 'Not set';
        } elseif ($user->role === 'firm' && $user->firm) {
            $profile['organization'] = $user->firm->org_name ?? 'Not set';
            $profile['org_type'] = $user->firm->org_type ?? 'Not set';
            $profile['contact_person'] = $user->firm->contact_person ?? 'Not set';
        } elseif ($user->role === 'college' && $user->college) {
            $profile['institution'] = $user->college->institution_name ?? 'Not set';
            $profile['address'] = $user->college->address ?? 'Not set';
            $profile['website'] = $user->college->website ?? 'Not set';
        } elseif ($user->role === 'mentor' && $user->mentor) {
            $profile['expertise'] = $user->mentor->expertise ?? 'Not set';
            $profile['qualification'] = $user->mentor->qualification ?? 'Not set';
        }

        return $profile;
    }

    private function toolGetUserEnrollments(): array
    {
        if (!Auth::check()) {
            return ['error' => 'User is not logged in. Please ask them to sign in first.'];
        }

        $enrollments = Enrollment::where('user_id', Auth::id())
            ->with('course:id,title,slug,venue,start_date,end_date,price,is_certified')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($e) => [
                'course_title' => $e->course?->title ?? 'Unknown',
                'course_slug' => $e->course?->slug ?? '',
                'type' => $e->type,
                'status' => $e->status,
                'payment_status' => $e->payment_status,
                'venue' => $e->course?->venue ?? $e->requested_venue ?? 'TBA',
                'start_date' => $e->course?->start_date ?? $e->proposed_start,
                'certificate_issued' => $e->certificate_issued ? 'Yes' : 'No',
                'enrolled_at' => $e->created_at?->format('M j, Y'),
            ])
            ->toArray();

        return $enrollments ?: ['message' => 'You have no enrollments yet.'];
    }

    private function toolGetEnrollmentStatus(array $args): array
    {
        if (!Auth::check()) {
            return ['error' => 'User is not logged in. Please ask them to sign in first.'];
        }

        $courseTitle = $args['course_title'] ?? '';
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->whereHas('course', fn($q) => $q->where('title', 'like', "%{$courseTitle}%"))
            ->with('course:id,title,slug,price')
            ->latest()
            ->first();

        if (!$enrollment) {
            return ['error' => 'No enrollment found for a course matching "' . $courseTitle . '".'];
        }

        return [
            'course_title' => $enrollment->course?->title,
            'course_slug' => $enrollment->course?->slug,
            'enrollment_status' => $enrollment->status,
            'payment_status' => $enrollment->payment_status,
            'type' => $enrollment->type,
            'total_amount' => $enrollment->total_amount ? '₹' . number_format($enrollment->total_amount, 2) : 'N/A',
            'college_note' => $enrollment->college_note ?? 'None',
            'enrolled_at' => $enrollment->created_at?->format('F j, Y'),
            'certificate_issued' => $enrollment->certificate_issued ? 'Yes' : 'No',
        ];
    }

    private function toolGetCertificateStatus(array $args): array
    {
        if (!Auth::check()) {
            return ['error' => 'User is not logged in. Please ask them to sign in first.'];
        }

        $courseTitle = $args['course_title'] ?? '';
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->whereHas('course', fn($q) => $q->where('title', 'like', "%{$courseTitle}%"))
            ->with('course:id,title,is_certified')
            ->latest()
            ->first();

        if (!$enrollment) {
            return ['error' => 'No enrollment found for a course matching "' . $courseTitle . '".'];
        }

        return [
            'course_title' => $enrollment->course?->title,
            'is_certified_course' => $enrollment->course?->is_certified ? 'Yes' : 'No',
            'certificate_issued' => $enrollment->certificate_issued ? 'Yes' : 'No',
            'certificate_code' => $enrollment->certificate_code ?? 'Not yet issued',
            'issued_at' => $enrollment->certificate_issued_at?->format('F j, Y') ?? 'N/A',
        ];
    }

    private function toolGetPlatformStats(): array
    {
        return [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_categories' => Category::count(),
            'total_colleges' => College::count(),
            'total_mentors' => Mentor::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_firms' => User::where('role', 'firm')->count(),
            'total_enrollments' => Enrollment::count(),
        ];
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