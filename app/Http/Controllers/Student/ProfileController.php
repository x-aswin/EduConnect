<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $student = Student::with('user')->where('user_id', Auth::id())->first();

        if (! $student) {
            return redirect()->route('student.complete.profile.edit');
        }

        return view('student.profile', compact('student'));
    }
    public function update(Request $request)
    {
        $request->validate([
            // user fields
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . Auth::id(),
            'current_password'      => 'nullable|current_password',
            'password'              => 'nullable|string|min:8|confirmed',

            // student fields
            'phone'                 => 'required|digits:10',
            'dob'                   => 'required|date|before:today',
            'gender'                => 'required|in:male,female,other',
            'current_qualification' => 'required|string|max:100',
            'address'               => 'required|string|max:500',
            'photo'                 => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'verification_doc'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Find the existing student record
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        // Update user (name / email / password)
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Handle Photo Upload: preserve existing when not provided
        $photoPath = $student->photo;
        if ($request->hasFile('photo')) {
            // delete old photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // Handle Verification Doc Upload: preserve existing when not provided
        $verificationDocPath = $student->verification_doc;
        if ($request->hasFile('verification_doc')) {
            // delete old document
            if ($verificationDocPath && Storage::disk('public')->exists($verificationDocPath)) {
                Storage::disk('public')->delete($verificationDocPath);
            }
            $verificationDocPath = $request->file('verification_doc')->store('students/verification_docs', 'public');
        }

        // Update with the validated data
        $student->update([
            'phone'                 => $request->phone,
            'dob'                   => $request->dob,
            'gender'                => $request->gender,
            'current_qualification' => $request->current_qualification,
            'address'               => $request->address,
            'photo'                 => $photoPath,
            'verification_doc'      => $verificationDocPath,
        ]);

        return redirect()->route('student.profile.edit')->with('success', 'Profile updated successfully!')->with('status', 'profile-updated');
    }
    public function CompleteEdit()
    {
        $student = Student::where('user_id', Auth::id())->first();

        if (! $student) {
            $student = new Student([
                'user_id' => Auth::id(),
                'phone' => null,
                'dob' => null,
                'gender' => null,
                'current_qualification' => null,
                'address' => null,
            ]);
        }

        return view('student.profile-complete', compact('student'));
    }
    public function CompleteUpdate(Request $request)
    {
        // Validate EVERY field you want to save
        $request->validate([
            'phone'                 => 'required|digits:10',
            'dob'                   => 'required|date|before:today',
            'gender'                => 'required|in:male,female,other',
            'current_qualification' => 'required|string|max:100',
            'address'               => 'required|string|max:500',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $student = Student::firstOrNew(['user_id' => Auth::id()]);

        if ($request->hasFile('photo')) {
            $student->photo = $request->file('photo')->store('students/photos', 'public');
        }

        if ($request->hasFile('verification_doc')) {
            $student->verification_doc = $request->file('verification_doc')->store('students/verification_docs', 'public');
        }

        $student->fill([
            'phone'                 => $request->phone,
            'dob'                   => $request->dob,
            'gender'                => $request->gender,
            'current_qualification' => $request->current_qualification,
            'address'               => $request->address,
        ]);

        $student->user_id = Auth::id();
        $student->save();

        return redirect()->route('dashboard')->with('success', 'Profile Completed!')->with('status', 'profile-completed');
    }
}
