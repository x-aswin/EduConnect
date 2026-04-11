<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CollegeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colleges = College::with('user')->latest()->get();
        return view('admin.manage-college', compact('colleges'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:pending,active,blocked',
            'institution_name' => 'required|string|max:255',
            'college_phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'required|file|mimes:pdf,jpeg,jpg,png|max:4096',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'college',
                'status' => $request->status,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('colleges/photos', 'public');
            }

            $verificationDocPath = $request->file('verification_doc')->store('colleges/verification-docs', 'public');

            College::create([
                'user_id' => $user->id,
                'institution_name' => $request->institution_name,
                'photo' => $photoPath,
                'college_phone' => $request->college_phone,
                'address' => $request->address,
                'website' => $request->website,
                'contact_person' => $request->contact_person,
                'designation' => $request->designation,
                'contact_number' => $request->contact_number,
                'verification_doc' => $verificationDocPath,
            ]);
        });

        return redirect()->route('admin.colleges.index')->with('success', 'College registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $colleges = College::with('user')->latest()->get();
        $editCollege = College::with('user')->findOrFail($id);
        $viewOnly = true;

        return view('admin.manage-college', compact('colleges', 'editCollege', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $colleges = College::with('user')->latest()->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editCollege = College::with('user')->findOrFail($id);
            return view('admin.manage-college', compact('colleges', 'editCollege'));
        }

        $deleteCollege = College::with('user')->findOrFail($id);
        return view('admin.manage-college', compact('colleges', 'deleteCollege', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $college = College::with('user')->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $college->user->id,
            'status' => 'required|in:pending,active,blocked',
            'institution_name' => 'required|string|max:255',
            'college_phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'verification_doc' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:4096',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $college) {
            $college->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ]);

            if ($request->filled('password')) {
                $college->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $updateData = [
                'institution_name' => $request->institution_name,
                'college_phone' => $request->college_phone,
                'address' => $request->address,
                'website' => $request->website,
                'contact_person' => $request->contact_person,
                'designation' => $request->designation,
                'contact_number' => $request->contact_number,
            ];

            if ($request->hasFile('photo')) {
                if ($college->photo && Storage::disk('public')->exists($college->photo)) {
                    Storage::disk('public')->delete($college->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('colleges/photos', 'public');
            }

            if ($request->hasFile('verification_doc')) {
                if ($college->verification_doc && Storage::disk('public')->exists($college->verification_doc)) {
                    Storage::disk('public')->delete($college->verification_doc);
                }
                $updateData['verification_doc'] = $request->file('verification_doc')->store('colleges/verification-docs', 'public');
            }

            $college->update($updateData);
        });

        return redirect()->route('admin.colleges.index')->with('success', 'College updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $college = College::with('user')->findOrFail($id);
        $user = $college->user;

        DB::transaction(function () use ($college, $user) {
            if ($college->photo && Storage::disk('public')->exists($college->photo)) {
                Storage::disk('public')->delete($college->photo);
            }

            if ($college->verification_doc && Storage::disk('public')->exists($college->verification_doc)) {
                Storage::disk('public')->delete($college->verification_doc);
            }

            $college->delete();
            $user->delete();
        });

        return redirect()->route('admin.colleges.index')->with('success', 'College and associated account deleted successfully!');
    }
}
