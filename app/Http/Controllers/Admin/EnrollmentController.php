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
    public function index()
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
            'requested_venue' => 'nullable|string|max:255',
            'proposed_schedule' => 'nullable|date',
            'participant_count' => 'nullable|integer|min:1',
            'total_amount' => 'nullable|numeric|min:0',
            'college_note' => 'nullable|string|max:1000',
        ]);

        $user = User::select('id', 'role')->findOrFail($validated['user_id']);
        $course = Course::select('id', 'course_type')->findOrFail($validated['course_id']);

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

        DB::transaction(function () use ($validated, $enrollmentType) {
            Enrollment::create([
                'user_id' => $validated['user_id'],
                'course_id' => $validated['course_id'],
                'type' => $enrollmentType,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'requested_venue' => $validated['requested_venue'] ?? null,
                'proposed_schedule' => $validated['proposed_schedule'] ?? null,
                'participant_count' => $enrollmentType === 'firm' ? ($validated['participant_count'] ?? null) : null,
                'total_amount' => $enrollmentType === 'firm' ? ($validated['total_amount'] ?? null) : null,
                'college_note' => $validated['college_note'] ?? null,
            ]);
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
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,rejected',
            'payment_status' => 'required|in:pending,paid,na',
            'college_note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($enrollment, $validated) {
            $enrollment->update([
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'college_note' => $validated['college_note'] ?? null,
            ]);
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
