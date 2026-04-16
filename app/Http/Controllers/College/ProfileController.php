<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        // $college = College::where('user_id', Auth::id())->firstOrFail();
        // return view('college.profile-complete', compact('college'));
        $user=User::find(Auth::id());
        return view('college.profile-complete', compact('user'));
    }
    public function update(Request $request)
    {
        // Validate EVERY field you want to save
        $request->validate([
            'institution_name'  => 'required|string|max:255',
            'college_phone'     => 'required|string|max:20',
            'website'           => 'nullable|url',
            'contact_person'    => 'required|string|max:255',
            'designation'       => 'required|string|max:100',
            'contact_number'    => 'required|string|max:20',
            'address'           => 'required|string|max:1000',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc'  => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // Find the existing college record
        //$college = \App\Models\College::where('user_id', Auth::id())->firstOrFail();

        // Handle Photo Upload if exists
        // $photoPath = "$college->photo;"

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('colleges/photos', 'public');
        }

        // Handle Verification Document Upload
        //$verificationDocPath = $college->verification_doc;
        if ($request->hasFile('verification_doc')) {
            $verificationDocPath = $request->file('verification_doc')->store('colleges/verifications', 'public');
        }
        
        // Update with the validated data
        $college=College::create([
            'user_id'           => Auth::id(),
            'institution_name'  => $request->institution_name,
            'college_phone'     => $request->college_phone,
            'website'           => $request->website,
            'contact_person'    => $request->contact_person,
            'designation'       => $request->designation,
            'contact_number'    => $request->contact_number,
            'address'           => $request->address,
            'photo'             => $photoPath,
            'verification_doc'  => $verificationDocPath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile Completed!');
    }
}
