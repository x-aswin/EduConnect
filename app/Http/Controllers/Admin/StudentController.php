<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $students = Student::with('user')->latest()->get();
        return view('admin.manage-student', compact('students'));
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
    //  Validation
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'status'   => 'required|in:active,blocked',
        'phone'    => 'required|digits:10',
        'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'verification_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        'dob'      => 'required|date',
        'gender'   => 'required|in:male,female,other',
        'current_qualification' => 'required|string|max:255',
        'address'  => 'required|string|max:255'

    ]);

    //  Database Transaction (Ensures both tables save or neither does)
    DB::transaction(function () use ($request) {
        
        // Create the Login Account
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'student',
            'status'   => $request->status,
        ]);

        // Handle Photo Upload if exists
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // Handle Verification Document Upload if exists
        $verificationDocPath = null;
        if ($request->hasFile('verification_doc')) {
            $verificationDocPath = $request->file('verification_doc')->store('students/verification_docs', 'public');
        }

        // Create the Student Profile linked to that User
        Student::create([
            'user_id'               => $user->id,
            'phone'                 => $request->phone,
            'dob'                   => $request->dob,
            'gender'                => $request->gender,
            'current_qualification' => $request->current_qualification,
            'address'               => $request->address,
            'photo'                 => $photoPath,
            'verification_doc'      => $verificationDocPath,
        ]);
    });

    return redirect()->route('admin.students.index')->with('success', 'Student registered successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $students = Student::with('user')->latest()->get();
        $editStudent = Student::with('user')->findOrFail($id);
        $viewOnly = true; // Flag to disable inputs
        
        return view('admin.manage-student', compact('students', 'editStudent', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $students = Student::with('user')->latest()->get(); // Still need the list for the table
        $isDeleteMode = $request->query('mode') === 'delete';

        if(!$isDeleteMode){
            $editStudent = Student::with('user')->findOrFail($id);
            return view('admin.manage-student', compact('students', 'editStudent'));
        }else{
            $deleteStudent = Student::with('user')->findOrFail($id); // The one we are editing
            return view('admin.manage-student', compact('students', 'deleteStudent', 'isDeleteMode'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::with('user')->findOrFail($id);

        // Validation - email should be unique except for current student
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $student->user->id,
            'status'   => 'required|in:active,blocked',
            'phone'    => 'required|digits:10',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'dob'      => 'required|date',
            'gender'   => 'required|in:male,female,other',
            'current_qualification' => 'required|string|max:255',
            'address'  => 'required|string|max:255'
        ];

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        // Database Transaction
        DB::transaction(function () use ($request, $student) {
            
            // Update User record
            $student->user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ]);

            // Update password only if provided
            if ($request->filled('password')) {
                $student->user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Handle Photo Upload
            $updateData = [
                'phone'                 => $request->phone,
                'dob'                   => $request->dob,
                'gender'                => $request->gender,
                'current_qualification' => $request->current_qualification,
                'address'               => $request->address,
            ];

            // delete old photo first
            if ($request->hasFile('photo')) {
                if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                    Storage::disk('public')->delete($student->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('students/photos', 'public');
            }

            // delete old verification document first
            if ($request->hasFile('verification_doc')) {
                if ($student->verification_doc && Storage::disk('public')->exists($student->verification_doc)) {
                    Storage::disk('public')->delete($student->verification_doc);
                }
                $updateData['verification_doc'] = $request->file('verification_doc')->store('students/verification_docs', 'public');
            }

            $student->update($updateData);
        });

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $student = Student::findOrFail($id);
    $user = $student->user;

    DB::transaction(function () use ($student, $user) {
        // 1. Delete the profile photo from storage if it exists
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        // Delete the verification document from storage if it exists
        if ($student->verification_doc && Storage::disk('public')->exists($student->verification_doc)) {
            Storage::disk('public')->delete($student->verification_doc);
        }

        // 2. Delete Student Profile
        $student->delete();

        // 3. Delete User Account
        $user->delete();
    });

    return redirect()->route('admin.students.index')->with('success', 'Student and associated account deleted successfully!');
}
}
