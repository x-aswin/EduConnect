<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    // public function create(): View
    // {
    //     return view('auth.register');
    // }
        public function create(Request $request): View
        {
            // Get the type from route defaults (or fallback to query string)
            $type = $request->route('type') ?? $request->query('type', 'learner');

            if ($type === 'college') {
                return view('auth.register-college');
            }

            return view('auth.register-learner');
        }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,college,firm'], // Validate the role
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        if ($request->role === 'student') {
        // \App\Models\Student::create([
        //     'user_id' => $user->id,
        // ]);
        } elseif ($request->role === 'college') {
            //only creating in user table, college table have not null on some values
            // \App\Models\College::create([
            //     'user_id' => $user->id,
            //     'institution_name' => $request->name,
            // ]);
        } elseif ($request->role === 'firm') {
            // \App\Models\Firm::create([
            //     'user_id' => $user->id,
            //     'org_name' => $request->name,
            // ]);
        } elseif ($request->role === 'mentor') {
            // \App\Models\Mentor::create([
            //     'user_id' => $user->id,
            // ]);
        }

        event(new Registered($user));

        Auth::login($user);


        if ($user->role === 'student') {
            return redirect()->route('student.complete.profile.edit');
        } elseif ($user->role === 'college') {
            return redirect()->route('college.complete.profile.edit');
        } elseif ($user->role === 'firm') {
            return redirect()->route('firm.complete.profile.edit');
        } elseif ($user->role === 'mentor') {
            return redirect()->route('/')->with('error', 'Profile incomplete!');
        }else {
        return redirect(route('dashboard', absolute: false));
        }
    }
}
