<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Firm;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'firm') {
            return redirect()->route('login');
        }

        $firm = $user->firm;

        return view('firm.profile', compact('user', 'firm'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'firm') {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
            // Allow only PDF or common image formats for verification docs
            'verification_doc' => 'nullable|mimes:pdf,jpg,jpeg,png,gif,svg|max:5120',
            'email' => 'nullable|email|max:255',
        ]);

        $firm = $user->firm ?: new Firm(['user_id' => $user->id]);

        $firm->org_name = $validated['org_name'];
        $firm->contact_person = $validated['contact_person'] ?? $firm->contact_person;
        $firm->designation = $validated['designation'] ?? $firm->designation;
        $firm->phone = $validated['phone'] ?? $firm->phone;
        $firm->address = $validated['address'] ?? $firm->address;

        // If an email was submitted, update the user's email (firms table doesn't have email column)
        if (!empty($validated['email'])) {
            if ($user->email !== $validated['email']) {
                $user->email = $validated['email'];
                $user->email_verified_at = null;
                $user->save();
            }
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('firms', 'public');
            $firm->photo = $path;
        }

        if ($request->hasFile('verification_doc')) {
            $path = $request->file('verification_doc')->store('firms/docs', 'public');
            $firm->verification_doc = $path;
        }

        $firm->save();

        return redirect()->route('firm.profile.edit')->with('success', 'Profile updated.');
    }

    // Show the initial complete-profile form (only used right after registration)
    public function completeEdit(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'firm') {
            return redirect()->route('login');
        }

        // show the same form but with a different view so the UX can be tailored
        return view('firm.profile-complete', ['user' => $user, 'firm' => $user->firm]);
    }

    public function completeUpdate(Request $request)
    {
        // reuse update logic for now; validation is the same
        return $this->update($request);
    }
}
