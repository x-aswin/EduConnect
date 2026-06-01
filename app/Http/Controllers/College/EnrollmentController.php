<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $enrollmentQuery = Enrollment::with(['user', 'course.college', 'participants'])
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            });

        if ($request->filled('course_id')) {
            $enrollmentQuery->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'rejected'], true)) {
            $enrollmentQuery->where('status', $request->status);
        }

        if ($request->filled('payment_status') && in_array($request->payment_status, ['na', 'pending', 'paid'], true)) {
            $enrollmentQuery->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $enrollmentQuery->where(function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhereHas('course', function ($courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', '%' . $search . '%');
                });
            });
        }

        $dateFilter = $request->get('date_filter', 'all');
        if ($dateFilter === 'today') {
            $enrollmentQuery->whereDate('created_at', now()->toDateString());
        } elseif ($dateFilter === 'last_7') {
            $enrollmentQuery->where('created_at', '>=', now()->subDays(7)->startOfDay());
        } elseif ($dateFilter === 'last_30') {
            $enrollmentQuery->where('created_at', '>=', now()->subDays(30)->startOfDay());
        } elseif ($dateFilter === 'last_90') {
            $enrollmentQuery->where('created_at', '>=', now()->subDays(90)->startOfDay());
        } elseif ($dateFilter === 'custom') {
            if ($request->filled('start_date')) {
                $enrollmentQuery->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $enrollmentQuery->whereDate('created_at', '<=', $request->end_date);
            }
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $enrollmentQuery->orderBy('created_at', 'asc');
        } else {
            $enrollmentQuery->orderBy('created_at', 'desc');
        }

        $enrollments = $enrollmentQuery->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->where('college_id', $college->id)
            ->orderBy('title')
            ->get();

        return view('college.manage-enrollment', compact('enrollments', 'users', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
            'requested_venue' => 'nullable|string|max:255',
            'proposed_schedule' => 'nullable|date',
            'college_note' => 'nullable|string|max:1000',
            'participants' => 'nullable|array',
            'participants.*.name' => 'nullable|string|max:255',
            'participants.*.contact_info' => 'nullable|string|max:255',
        ]);

        $user = User::select('id', 'role')->findOrFail($validated['user_id']);
        $course = Course::select('id', 'college_id', 'course_type', 'price')
            ->where('college_id', $college->id)
            ->findOrFail($validated['course_id']);

        if (!in_array($user->role, ['student', 'firm'], true)) {
            return back()->withErrors([
                'user_id' => 'Selected user must be a student or a firm.',
            ])->withInput();
        }

        $expectedCourseType = $user->role === 'firm' ? 'firm_only' : 'student_only';
        if ($course->course_type !== $expectedCourseType) {
            return back()->withErrors([
                'course_id' => 'Selected course type does not match the selected user type.',
            ])->withInput();
        }

        $enrollmentType = $user->role === 'firm' ? 'firm' : 'student';

        $participants = collect($validated['participants'] ?? [])
            ->map(function ($participant) {
                return [
                    'name' => trim((string) ($participant['name'] ?? '')),
                    'contact_info' => trim((string) ($participant['contact_info'] ?? '')),
                ];
            })
            ->filter(function ($participant) {
                return $participant['name'] !== '' || $participant['contact_info'] !== '';
            })
            ->values();

        if ($enrollmentType === 'firm' && $participants->isEmpty()) {
            return back()->withErrors([
                'participants' => 'At least one participant is required for firm enrollments.',
            ])->withInput();
        }

        if ($participants->contains(fn ($participant) => $participant['name'] === '' || $participant['contact_info'] === '')) {
            return back()->withErrors([
                'participants' => 'Each participant must include both name and contact info.',
            ])->withInput();
        }

        $participantCount = $enrollmentType === 'firm' ? $participants->count() : null;

        $calculatedTotalAmount = $enrollmentType === 'firm'
            ? ((float) $course->price * (int) ($participantCount ?? 0))
            : (float) $course->price;

        DB::transaction(function () use ($validated, $enrollmentType, $participantCount, $calculatedTotalAmount, $participants, $course) {
            $enrollment = Enrollment::create([
                'user_id' => $validated['user_id'],
                'course_id' => $validated['course_id'],
                'type' => $enrollmentType,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'requested_venue' => $validated['requested_venue'] ?? null,
                'proposed_schedule' => $validated['proposed_schedule'] ?? null,
                'college_note' => $enrollmentType === 'firm' ? ($validated['college_note'] ?? null) : null,
                'participant_count' => $participantCount,
                'total_amount' => $calculatedTotalAmount,
            ]);

            // If created with confirmed status, decrement seats
            if ($validated['status'] === 'confirmed') {
                $seatCount = $enrollmentType === 'student' ? 1 : ($participantCount ?? 1);
                $course->available_seats = max(0, $course->available_seats - $seatCount);
                $course->save();
            }

            if ($enrollmentType === 'firm' && $participants->isNotEmpty()) {
                $enrollment->participants()->createMany($participants->all());
            }
        });

        return redirect()->route('college.enrollments.index')->with('success', 'Enrollment added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $enrollments = Enrollment::with(['user', 'course.college', 'participants'])
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            })
            ->latest()
            ->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->where('college_id', $college->id)
            ->orderBy('title')
            ->get();

        $editEnrollment = Enrollment::with(['user', 'course.college', 'participants'])
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            })
            ->findOrFail($id);

        $viewOnly = true;

        return view('college.manage-enrollment', compact('enrollments', 'users', 'courses', 'editEnrollment', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $enrollments = Enrollment::with(['user', 'course.college', 'participants'])
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            })
            ->latest()
            ->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->where('college_id', $college->id)
            ->orderBy('title')
            ->get();

        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editEnrollment = Enrollment::with(['user', 'course.college', 'participants'])
                ->whereHas('course', function ($query) use ($college) {
                    $query->where('college_id', $college->id);
                })
                ->findOrFail($id);

            return view('college.manage-enrollment', compact('enrollments', 'users', 'courses', 'editEnrollment'));
        }

        $deleteEnrollment = Enrollment::with(['user', 'course.college'])
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            })
            ->findOrFail($id);

        return view('college.manage-enrollment', compact('enrollments', 'users', 'courses', 'deleteEnrollment', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $enrollment = Enrollment::with('course')
            ->whereHas('course', function ($query) use ($college) {
                $query->where('college_id', $college->id);
            })
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
            'college_note' => 'nullable|string|max:1000',
            'participants' => 'nullable|array',
            'participants.*.name' => 'nullable|string|max:255',
            'participants.*.contact_info' => 'nullable|string|max:255',
        ]);

        $participants = collect($validated['participants'] ?? [])
            ->map(function ($participant) {
                return [
                    'name' => trim((string) ($participant['name'] ?? '')),
                    'contact_info' => trim((string) ($participant['contact_info'] ?? '')),
                ];
            })
            ->filter(function ($participant) {
                return $participant['name'] !== '' || $participant['contact_info'] !== '';
            })
            ->values();

        if ($enrollment->type === 'firm') {
            if ($participants->isEmpty()) {
                return back()->withErrors([
                    'participants' => 'At least one participant is required for firm enrollments.',
                ])->withInput();
            }

            if ($participants->contains(fn ($participant) => $participant['name'] === '' || $participant['contact_info'] === '')) {
                return back()->withErrors([
                    'participants' => 'Each participant must include both name and contact info.',
                ])->withInput();
            }
        }

        DB::transaction(function () use ($enrollment, $validated, $participants) {
            $oldStatus = $enrollment->status;
            $newStatus = $validated['status'];
            $course = $enrollment->course;
            $seatCount = $enrollment->type === 'student' ? 1 : ($enrollment->participant_count ?? 1);

            // Handle seat count changes based on status transitions
            if ($oldStatus !== 'confirmed' && $newStatus === 'confirmed') {
                // Moving to confirmed: decrement available seats
                $course->available_seats = max(0, $course->available_seats - $seatCount);
                $course->save();
            } elseif ($oldStatus === 'confirmed' && $newStatus !== 'confirmed') {
                // Moving away from confirmed: increment available seats back
                $course->available_seats += $seatCount;
                $course->save();
            }

            $participantCount = $enrollment->type === 'firm' ? $participants->count() : null;
            $totalAmount = $enrollment->type === 'firm'
                ? ((float) $enrollment->course->price * (int) ($participantCount ?? 0))
                : (float) $enrollment->course->price;

            $enrollment->update([
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'college_note' => $enrollment->type === 'firm' ? ($validated['college_note'] ?? null) : null,
                'participant_count' => $participantCount,
                'total_amount' => $totalAmount,
            ]);

            if ($enrollment->type === 'firm') {
                $enrollment->participants()->delete();
                $enrollment->participants()->createMany($participants->all());
            }
        });

        return redirect()->route('college.enrollments.index')->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $college = College::where('user_id', Auth::id())->firstOrFail();

        $enrollment = Enrollment::whereHas('course', function ($query) use ($college) {
            $query->where('college_id', $college->id);
        })->findOrFail($id);

        DB::transaction(function () use ($enrollment) {
            $enrollment->delete();
        });

        return redirect()->route('college.enrollments.index')->with('success', 'Enrollment deleted successfully!');
    }
}
