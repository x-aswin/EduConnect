<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentors = Mentor::with(['user', 'college.user'])->latest()->get();
        $colleges = College::with('user')->latest()->get();

        return view('admin.manage-mentor', compact('mentors', 'colleges'));
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
            'college_id' => 'required|exists:colleges,id',
            'qualification' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mentor',
                'status' => $request->status,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('mentors/photos', 'public');
            }

            Mentor::create([
                'user_id' => $user->id,
                'college_id' => $request->college_id,
                'qualification' => $request->qualification,
                'expertise' => $request->expertise,
                'bio' => $request->bio,
                'photo' => $photoPath,
            ]);
        });

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mentors = Mentor::with(['user', 'college.user'])->latest()->get();
        $colleges = College::with('user')->latest()->get();
        $editMentor = Mentor::with(['user', 'college.user'])->findOrFail($id);
        $viewOnly = true;

        return view('admin.manage-mentor', compact('mentors', 'colleges', 'editMentor', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $mentors = Mentor::with(['user', 'college.user'])->latest()->get();
        $colleges = College::with('user')->latest()->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editMentor = Mentor::with(['user', 'college.user'])->findOrFail($id);
            return view('admin.manage-mentor', compact('mentors', 'colleges', 'editMentor'));
        }

        $deleteMentor = Mentor::with(['user', 'college.user'])->findOrFail($id);
        return view('admin.manage-mentor', compact('mentors', 'colleges', 'deleteMentor', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mentor = Mentor::with('user')->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mentor->user->id,
            'status' => 'required|in:pending,active,blocked',
            'college_id' => 'required|exists:colleges,id',
            'qualification' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $mentor) {
            $mentor->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
            ]);

            if ($request->filled('password')) {
                $mentor->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $updateData = [
                'college_id' => $request->college_id,
                'qualification' => $request->qualification,
                'expertise' => $request->expertise,
                'bio' => $request->bio,
            ];

            if ($request->hasFile('photo')) {
                if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                    Storage::disk('public')->delete($mentor->photo);
                }
                $updateData['photo'] = $request->file('photo')->store('mentors/photos', 'public');
            }

            $mentor->update($updateData);
        });

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mentor = Mentor::with('user')->findOrFail($id);
        $user = $mentor->user;

        DB::transaction(function () use ($mentor, $user) {
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }

            $mentor->delete();
            $user->delete();
        });

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor and associated account deleted successfully!');
    }
}
