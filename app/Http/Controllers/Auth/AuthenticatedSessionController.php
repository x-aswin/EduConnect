<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->session()->put('notifications.last_seen_at', now()->toIso8601String());

        $user = Auth::user();

        // $profileExists = match ($user->role) {
        //     'student' => $user->student()->exists(),
        //     'mentor' => $user->mentor()->exists(),
        //     'college' => $user->college()->exists(),
        //     'firm' => $user->firm()->exists(),
        //     'admin' => true,
        //     default => false,
        // };
        // if (!$profileExists) {
        //     return match ($user->role) {
        //         'college' => redirect()->route('college.complete.profile.edit'),
        //         'student' => redirect()->route('student.complete.profile.edit'),
        //         'firm' => redirect()->route('firm.complete.profile.edit'),
        //         'mentor' => redirect('/')->with('error', 'Profile incomplete!'),
        //         default => redirect('/'),
        //     };
        // }

        // Redirect based on role.
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'college' => redirect()->route('college.dashboard'),
            'mentor' => redirect()->route('mentor.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'firm' => redirect()->route('firm.dashboard'),
            default => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
