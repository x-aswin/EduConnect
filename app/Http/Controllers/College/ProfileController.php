<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $college = College::with('user')->where('user_id', Auth::id())->first();

        if (! $college) {
            return redirect()->route('college.complete.profile.edit');
        }

        return view('college.profile', compact('college'));
    }

    public function update(Request $request)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'acronym'          => 'required|string|max:255',
            'institution_name'  => 'required|string|max:255',
            'college_phone'     => 'required|string|max:20',
            'website'           => 'nullable|url',
            'contact_person'    => 'required|string|max:255',
            'designation'       => 'required|string|max:100',
            'contact_number'    => 'required|string|max:20',
            'address'           => 'required|string|max:1000',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $photoPath = $college->photo;
        $verificationDocPath = $college->verification_doc;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('colleges/photos', 'public');
        }

        if ($request->hasFile('verification_doc')) {
            if ($verificationDocPath) {
                Storage::disk('public')->delete($verificationDocPath);
            }
            $verificationDocPath = $request->file('verification_doc')->store('colleges/verifications', 'public');
        }

        if ($user = User::find(Auth::id())) {
            $user->update([
                'name' => $request->acronym,
            ]);
        }

        $college->update([
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

        return redirect()->route('college.profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function completeEdit()
    {
        $college = College::with('user')->where('user_id', Auth::id())->first();

        if (! $college) {
            $college = new College([
                'institution_name' => Auth::user()?->name,
            ]);
        }

        return view('college.profile-complete', compact('college'));
    }

    public function completeUpdate(Request $request)
    {
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

        $college = College::firstOrNew(['user_id' => Auth::id()]);

        if ($request->hasFile('photo')) {
            $college->photo = $request->file('photo')->store('colleges/photos', 'public');
        }

        if ($request->hasFile('verification_doc')) {
            $college->verification_doc = $request->file('verification_doc')->store('colleges/verifications', 'public');
        }

        $college->fill([
            'institution_name'  => $request->institution_name,
            'college_phone'     => $request->college_phone,
            'website'           => $request->website,
            'contact_person'    => $request->contact_person,
            'designation'       => $request->designation,
            'contact_number'    => $request->contact_number,
            'address'           => $request->address,
        ]);

        $college->user_id = Auth::id();
        $college->save();

        // Update the authenticated user's status to 'pending'
        $user = Auth::user();
        if ($user) {
            $user->update([
                'status' => 'pending',
            ]);
        }

        return redirect()->route('college.dashboard')->with('success', 'Profile Completed!');
    }
}
