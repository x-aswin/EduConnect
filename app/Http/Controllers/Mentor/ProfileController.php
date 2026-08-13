<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the mentor's profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        $mentor = Mentor::with('college')->where('user_id', $user->id)->firstOrFail();

        return view('mentor.profile', compact('user', 'mentor'));
    }

    /**
     * Update the mentor's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'qualification' => 'required|string|max:255',
            'expertise'     => 'required|string|max:255',
            'bio'           => 'nullable|string|max:1000',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle Photo Upload
        $photoPath = $mentor->photo;
        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('mentors/photos', 'public');
        }

        // Update User Model (Name & Email)
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Update Mentor Model
        $mentor->update([
            'qualification' => $request->qualification,
            'expertise'     => $request->expertise,
            'bio'           => $request->bio,
            'photo'         => $photoPath,
        ]);

        return redirect()->route('mentor.profile.edit')->with('success', 'Profile updated successfully!');
    }
}
