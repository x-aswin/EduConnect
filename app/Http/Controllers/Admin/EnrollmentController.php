<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status', 'all');
        $paymentStatus = $request->get('payment_status', 'all');
        $type = $request->get('type', 'all');

        $enrollments = Enrollment::with(['user', 'course.college', 'participants'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhereHas('college', function ($collegeQuery) use ($search) {
                                $collegeQuery->where('institution_name', 'like', '%' . $search . '%');
                            });
                    })
                    ->orWhere('requested_venue', 'like', '%' . $search . '%')
                    ->orWhere('proposed_time', 'like', '%' . $search . '%')
                    ->orWhereHas('participants', function ($participantQuery) use ($search) {
                        $participantQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('contact_info', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when(in_array($status, ['pending', 'confirmed', 'rejected'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(in_array($paymentStatus, ['pending', 'paid', 'na'], true), function ($query) use ($paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->when(in_array($type, ['student', 'firm'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->orderBy('title')
            ->get();

        return view('admin.manage-enrollment', compact('enrollments', 'users', 'courses'));
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
        // 1. Core validation to ensure relationships exist
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
        ]);

        // Fetch full models so we can access firm_duration and available_seats
        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);

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

        // 2. Validate strict scheduling logistics if this is a firm booking
        $rules = [];
        if ($enrollmentType === 'firm') {
            $rules = [
                'requested_venue' => 'required|string|max:255',
                'proposed_start'  => 'required|date',
                'proposed_time'   => 'required|string|max:255',
                'proposed_end'    => [
                    'required',
                    'date',
                    'after_or_equal:proposed_start',
                    function ($attribute, $value, $fail) use ($request, $course) {
                        $startStr = $request->input('proposed_start');
                        if (!$startStr || !$value || !$course->firm_duration) {
                            return;
                        }

                        $start = new \DateTime($startStr);
                        $end = new \DateTime($value);
                        
                        $diffDays = (int) $start->diff($end)->format('%a') + 1;
                        $expected = (int) $course->firm_duration;

                        if ($diffDays !== $expected) {
                            $fail("The duration between start and end dates must be exactly {$expected} days. Currently selected: {$diffDays} days.");
                        }
                    }
                ],
                'participants' => 'nullable|array',
                'participants.*.name' => 'nullable|string|max:255',
                'participants.*.contact_info' => 'nullable|string|max:255',
            ];
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        // 3. Process the participants logic
        $participants = collect($request->input('participants', []))
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

        // 4. Save to the database
        DB::transaction(function () use ($request, $enrollmentType, $participantCount, $calculatedTotalAmount, $participants, $course) {
            $enrollment = Enrollment::create([
                'user_id'           => $request->user_id,
                'course_id'         => $request->course_id,
                'type'              => $enrollmentType,
                'status'            => $request->status,
                'payment_status'    => $request->payment_status,
                'requested_venue'   => $enrollmentType === 'firm' ? $request->requested_venue : null,
                'proposed_start'    => $enrollmentType === 'firm' ? $request->proposed_start : null,
                'proposed_end'      => $enrollmentType === 'firm' ? $request->proposed_end : null,
                'proposed_time'     => $enrollmentType === 'firm' ? $request->proposed_time : null,
                'participant_count' => $participantCount,
                'total_amount'      => $calculatedTotalAmount,
            ]);

            // If created with confirmed status, decrement seats safely
            if ($request->status === 'confirmed') {
                $seatCount = $enrollmentType === 'student' ? 1 : ($participantCount ?? 1);
                $course->available_seats = max(0, $course->available_seats - $seatCount);
                $course->save();
            }

            if ($enrollmentType === 'firm' && $participants->isNotEmpty()) {
                $enrollment->participants()->createMany($participants->all());
            }
        });

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment added successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $enrollments = Enrollment::with(['user', 'course.college', 'participants'])
            ->latest()
            ->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->orderBy('title')
            ->get();

        $editEnrollment = Enrollment::with(['user', 'course.college', 'participants'])
            ->findOrFail($id);

        $viewOnly = true;

        return view('admin.manage-enrollment', compact('enrollments', 'users', 'courses', 'editEnrollment', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $enrollments = Enrollment::with(['user', 'course.college', 'participants'])
            ->latest()
            ->get();

        $users = User::whereIn('role', ['student', 'firm'])
            ->orderBy('name')
            ->get();

        $courses = Course::with('college')
            ->orderBy('title')
            ->get();

        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editEnrollment = Enrollment::with(['user', 'course.college', 'participants'])
                ->findOrFail($id);

            return view('admin.manage-enrollment', compact('enrollments', 'users', 'courses', 'editEnrollment'));
        }

        $deleteEnrollment = Enrollment::with(['user', 'course.college'])
            ->findOrFail($id);

        return view('admin.manage-enrollment', compact('enrollments', 'users', 'courses', 'deleteEnrollment', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
    {
        $enrollment = Enrollment::with('course')->findOrFail($id);

        // 1. Base validation rules
        $rules = [
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
        ];

        // 2. Add firm-specific rules (including your duration validation)
        if ($enrollment->type === 'firm') {
            $rules['requested_venue'] = 'required|string|max:255';
            $rules['proposed_start']  = 'required|date';
            $rules['proposed_time']   = 'required|string|max:255';
            $rules['proposed_end']    = [
                'required',
                'date',
                'after_or_equal:proposed_start',
                function ($attribute, $value, $fail) use ($request, $enrollment) {
                    $startStr = $request->input('proposed_start');
                    $course = $enrollment->course;

                    if (!$startStr || !$value || !$course || !$course->firm_duration) {
                        return;
                    }

                    $start = new \DateTime($startStr);
                    $end = new \DateTime($value);
                    
                    // Calculate inclusive duration in days
                    $diffDays = (int) $start->diff($end)->format('%a') + 1;
                    $expected = (int) $course->firm_duration;

                    if ($diffDays !== $expected) {
                        $fail("The duration between start and end dates must be exactly {$expected} days. Currently selected: {$diffDays} days.");
                    }
                }
            ];
            $rules['participants'] = 'nullable|array';
            $rules['participants.*.name'] = 'nullable|string|max:255';
            $rules['participants.*.contact_info'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        // 3. Process the participants logic (safely fallback to empty array)
        $participants = collect($request->input('participants', []))
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

        // 4. Save to the database
        DB::transaction(function () use ($request, $enrollment, $validated, $participants) {
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

            // Prepare update array
            $updateData = [
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'participant_count' => $participantCount,
                'total_amount' => $totalAmount,
            ];

            // Only update schedule/venue data if this is a firm booking
            if ($enrollment->type === 'firm') {
                $updateData['requested_venue'] = $request->input('requested_venue');
                $updateData['proposed_start']  = $request->input('proposed_start');
                $updateData['proposed_end']    = $request->input('proposed_end');
                $updateData['proposed_time']   = $request->input('proposed_time');
            }

            $enrollment->update($updateData);

            if ($enrollment->type === 'firm') {
                $enrollment->participants()->delete();
                $enrollment->participants()->createMany($participants->all());
            }
        });

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        DB::transaction(function () use ($enrollment) {
            $enrollment->delete();
        });

        return redirect()->route('admin.enrollments.index')->with('success', 'Enrollment deleted successfully!');
    }
}
