<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\FirmParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseController extends Controller
{
     public function index(Request $request)
    {
        $query = Course::with(['college.user', 'category'])
            ->where('course_type', 'firm_only')
            ->where('status', 'active');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('venue', 'like', '%' . $keyword . '%')
                    ->orWhereHas('college', function ($collegeQuery) use ($keyword) {
                        $collegeQuery->where('institution_name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($userQuery) use ($keyword) {
                                $userQuery->where('name', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $categoryName = trim((string) $request->category);
            $query->whereHas('category', function ($categoryQuery) use ($categoryName) {
                $categoryQuery->where('name', $categoryName);
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
        } elseif ($sort === 'start_soon') {
            $query->orderBy('start_date', 'asc')->orderBy('created_at', 'desc');
        } else {
            $query->latest();
            $sort = 'newest';
        }

        $courses = $query
            ->paginate(9)
            ->withQueryString()
            ->through(function (Course $course) {
                $gradients = [
                    'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                    'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                    'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                    'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                ];

                $icons = [
                    'bi-shield-shaded', 'bi-graph-up-arrow', 'bi-palette2',
                    'bi-bar-chart-line', 'bi-code-slash', 'bi-camera-video',
                ];

                $colors = ['text-success', 'text-danger', 'text-warning', 'text-primary', 'text-info'];

                $visuals = [
                    'image_bg' => $gradients[array_rand($gradients)],
                    'icon' => $icons[array_rand($icons)],
                    'icon_color' => $colors[array_rand($colors)],
                ];

                $imagePath = $course->course_image ?? $course->college?->photo ?? null;
                $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : null;

                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'image' => $imageUrl,
                    'category' => $course->category?->name ?? null,
                    'college' => $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College',
                    'venue' => $course->venue ?? 'TBA',
                    'start_date' => $course->start_date ? Carbon::parse($course->start_date)->format('M d, Y') : 'TBA',
                    'price' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seats_total' => (int) ($course->total_seats ?? 0),
                    'seats_available' => (int) ($course->available_seats ?? 0),
                    'type' => $course->course_type,
                    ...$visuals,
                ];
            });

        $categories = Category::query()
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->values();

        return view('firm.browse-course', compact('courses', 'categories', 'sort'));
    }
       public function show(string $slug)
    {
        $course = Course::with(['college', 'category'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
        $isEnrolled = false;
        return view('firm.course-details', ['course' => $course, 'isEnrolled' => $isEnrolled]);
    }
    public function book(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'firm') {
            return redirect()->route('login');
        }

        // Only allow booking for firm_only courses
        if ($course->course_type !== 'firm_only') {
            return back()->with('error', 'This course is not available for firm booking.');
        }

        // Validate the form input
        $validated = $request->validate([
            'requested_venue'    => 'required|string|max:255',
            'proposed_schedule'  => 'required|date',
            'participant_count'  => 'nullable|integer|min:1',
            'participants'       => 'nullable|array',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.contact_info' => 'nullable|string|max:255',
            'college_note'       => 'nullable|string|max:500',
        ]);

        $submittedParticipants = $request->input('participants', []);
        $submittedCount = is_array($submittedParticipants) ? count($submittedParticipants) : 0;
        $requestedCount = isset($validated['participant_count']) ? (int) $validated['participant_count'] : 0;
        $finalCount = $submittedCount > 0 ? $submittedCount : max($requestedCount, 1);

        $unitPrice = (float) ($course->price ?? 0);
        $totalAmount = $unitPrice * $finalCount;

        DB::transaction(function () use ($course, $user, $validated, $finalCount, $totalAmount, $submittedParticipants) {
            $enrollment = Enrollment::create([
                'course_id'         => $course->id,
                'user_id'           => $user->id,
                'type'              => 'firm',
                'status'            => 'pending',
                'requested_venue'   => $validated['requested_venue'],
                'proposed_schedule' => $validated['proposed_schedule'],
                'participant_count' => $finalCount,
                'total_amount'      => $totalAmount,
                'payment_status'    => 'na',
                'college_note'      => $validated['college_note'] ?? null,
            ]);

            if (is_array($submittedParticipants) && count($submittedParticipants) > 0) {
                foreach ($submittedParticipants as $p) {
                    if (empty($p['name'])) continue;
                    FirmParticipant::create([
                        'enrollment_id' => $enrollment->id,
                        'name' => $p['name'],
                        'contact_info' => $p['contact_info'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('firm.bookings.index')
            ->with('success', 'Booking request submitted successfully.');
    }
    function bookings()
    {
        $user = Auth::user();
        $bookings = Enrollment::with(['course.college.user', 'course.mentor.user', 'participants'])
            ->where('user_id', $user->id)
            ->where('type', 'firm')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('firm.bookings', compact('bookings'));
    }

    /**
     * Show a single enrollment and its participants for management.
     */
    public function bookingShow(Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }

        $participants = $enrollment->participants()->orderBy('created_at')->get();
        $mode = request()->get('mode', 'view');

        return view('firm.bookings', compact('enrollment', 'participants', 'mode'));
    }

    /**
     * Update a pending booking before college approval.
     */
    public function updateBooking(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }

        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'This booking can only be edited before approval.');
        }

        $validated = $request->validate([
            'requested_venue' => 'required|string|max:255',
            'proposed_schedule' => 'required|date',
            'college_note' => 'nullable|string|max:500',
            'participants' => 'nullable|array',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.contact_info' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($enrollment, $validated, $request) {
            $participantCount = $enrollment->participants()->count();

            $enrollment->update([
                'requested_venue' => $validated['requested_venue'],
                'proposed_schedule' => $validated['proposed_schedule'],
                'college_note' => $validated['college_note'] ?? null,
                'participant_count' => max($participantCount, 1),
                'total_amount' => (float) ($enrollment->course->price ?? 0) * max($participantCount, 1),
            ]);
        });

        return redirect()->route('firm.bookings.show', ['enrollment' => $enrollment, 'mode' => 'edit'])
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Store a participant for an enrollment.
     */
    public function storeParticipant(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ]);

        FirmParticipant::create([
            'enrollment_id' => $enrollment->id,
            'name' => $validated['name'],
            'contact_info' => $validated['contact_info'] ?? null,
        ]);

        $enrollment->refresh();
        $enrollment->participant_count = $enrollment->participants()->count();
        $enrollment->total_amount = (float) ($enrollment->course->price ?? 0) * (int) $enrollment->participant_count;
        $enrollment->save();

        return redirect()->route('firm.bookings.show', $enrollment)->with('success', 'Participant added successfully.');
    }

    /**
     * Remove a participant from an enrollment.
     */
    public function destroyParticipant(Request $request, Enrollment $enrollment, FirmParticipant $participant)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }
        if ($participant->enrollment_id !== $enrollment->id) {
            abort(404);
        }

        FirmParticipant::query()->whereKey($participant->id)->delete();

        $enrollment->refresh();
        $enrollment->participant_count = $enrollment->participants()->count();
        $enrollment->total_amount = (float) ($enrollment->course->price ?? 0) * (int) $enrollment->participant_count;
        $enrollment->save();

        return redirect()->route('firm.bookings.show', $enrollment)->with('success', 'Participant removed.');
    }

    /**
     * Update a participant entry while booking is still pending.
     */
    public function updateParticipant(Request $request, Enrollment $enrollment, FirmParticipant $participant)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }
        if ($participant->enrollment_id !== $enrollment->id) {
            abort(404);
        }
        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Participant editing is only available before approval.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
        ]);

        $participant->update([
            'name' => $validated['name'],
            'contact_info' => $validated['contact_info'] ?? null,
        ]);

        return redirect()->route('firm.bookings.show', ['enrollment' => $enrollment, 'mode' => 'edit'])
            ->with('success', 'Participant updated successfully.');
    }

    /**
     * Show the firm payment page once the booking is approved.
     */
    public function payment(Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user || $enrollment->user_id !== $user->id || $enrollment->type !== 'firm') {
            abort(403);
        }

        if ($enrollment->status !== 'confirmed') {
            return back()->with('error', 'Booking is not approved yet.');
        }

        if ($enrollment->payment_status === 'paid') {
            return back()->with('info', 'Payment is already completed or not required.');
        }

        $enrollment->loadMissing(['course.college.user', 'participants']);

        return view('firm.payment', ['enrollment' => $enrollment]);
    }

    /**
     * Process a simulated payment and mark enrollment as paid.
     */
    public function processPayment(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if ($enrollment->user_id !== $user->id || $enrollment->type !== 'firm') abort(403);

        if ($enrollment->status !== 'confirmed' || $enrollment->payment_status === 'paid') {
            return back()->with('error', 'Payment cannot be processed for this booking.');
        }

        $enrollment->payment_status = 'paid';
        $enrollment->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Payment recorded — access granted.',
                'redirect' => route('firm.bookings.index'),
            ]);
        }

        return redirect()->route('firm.bookings.index')->with('success', 'Payment recorded — access granted.');
    }

    /**
     * Remove a pending enrollment (booking) for the firm user.
     */
    public function destroy(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if ($enrollment->user_id !== $user->id || $enrollment->type !== 'firm') abort(403);

        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'The booking was already approved or rejected; cannot remove.');
        }

        $deleted = Enrollment::query()
            ->where('id', $enrollment->id)
            ->where('user_id', $user->id)
            ->where('type', 'firm')
            ->where('status', 'pending')
            ->delete();

        if (!$deleted) return back()->with('error', 'Unable to remove this booking.');
        return redirect()->route('firm.bookings.index')->with('success', 'Booking removed.');
    }
}
