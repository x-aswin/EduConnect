<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if(!Auth::check()){
            return redirect()->route('login');
        }
        $user = Auth::user();
        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is pending admin approval.']);
        }
        if ($user->status === 'blocked') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been blocked. Please contact support.']);
        }

        // Skip profile check for complete-profile routes
        $allowedRoutes = ['college.complete.profile.edit', 'college.complete.profile.update', 'student.complete.profile.edit', 'student.complete.profile.update', 'firm.complete.profile.edit', 'firm.complete.profile.update'];
        if (!in_array($request->route()->getName(), $allowedRoutes)) {
            $profileExists = match ($user->role) {
                'student' => $user->student()->exists(),
                'mentor' => $user->mentor()->exists(),
                'college' => $user->college()->exists(),
                'firm' => $user->firm()->exists(),
                'admin' => true,
                default => false,
            };
            if (!$profileExists) {
                return match ($user->role) {
                    'college' => redirect()->route('college.complete.profile.edit'),
                    'student' => redirect()->route('student.complete.profile.edit'),
                    'firm' => redirect()->route('firm.complete.profile.edit'),
                    'mentor' => redirect('/')->with('error', 'Profile incomplete!'),
                    default => redirect('/'),
                };
            }
        }

        // required role check
        if ($user->role !== $role) {
            // Redirect them to their own dashboard
            return match($user->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'college' => redirect()->route('college.dashboard'),
                'mentor'  => redirect()->route('mentor.dashboard'),
                'student' => redirect()->route('student.dashboard'),
                'firm'    => redirect()->route('firm.dashboard'),
                default   => redirect('/'),
            };
        }

        return $next($request);
    }
}
