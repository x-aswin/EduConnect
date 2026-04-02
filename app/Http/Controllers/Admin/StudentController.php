<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.manage-student');
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
        'phone'    => 'nullable|digits:10',
        'photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    //  Database Transaction (Ensures both tables save or neither does)
    DB::transaction(function () use ($request) {
        
        // Create the Login Account
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'student',
            'status'   => 'active',
        ]);

        // Handle Photo Upload if exists
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
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
        ]);
    });

    return redirect()->route('admin.students.index')->with('success', 'Student registered successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
