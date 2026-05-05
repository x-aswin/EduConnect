<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        return view('student.profile-complete', compact('student'));
    }
    public function update(Request $request)
    {
        // Validate EVERY field you want to save
        $request->validate([
            'phone'                 => 'required|digits:10',
            'dob'                   => 'required|date|before:today',
            'gender'                => 'required|in:male,female,other',
            'current_qualification' => 'required|string|max:100',
            'address'               => 'required|string|max:500',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Find the existing student record
        $student = \App\Models\Student::where('user_id',Auth::id())->firstOrFail();

        // Handle Photo Upload if exists
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }
        
        // Update with the validated data
        $student->update([
            'phone'                 => $request->phone,
            'dob'                   => $request->dob,
            'gender'                => $request->gender,
            'current_qualification' => $request->current_qualification,
            'address'               => $request->address,
            'photo'                 => $photoPath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile Completed!');
    }
}
