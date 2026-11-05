<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_marks_user_verified_when_email_verification_is_disabled(): void
    {
        Setting::where('key', 'email_verification_enabled')->update(['value' => '0']);

        $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'test.student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
        ]);

        $this->assertNotNull(User::where('email', 'test.student@example.com')->first()->email_verified_at);
    }

    public function test_unverified_users_can_access_verified_routes_when_disabled(): void
    {
        Setting::where('key', 'email_verification_enabled')->update(['value' => '0']);
        $user = User::factory()->unverified()->create(['role' => 'student']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_login_does_not_redirect_to_verification_when_disabled(): void
    {
        Setting::where('key', 'email_verification_enabled')->update(['value' => '0']);
        $user = User::factory()->unverified()->create(['role' => 'student']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_login_still_requires_verification_when_enabled(): void
    {
        Setting::where('key', 'email_verification_enabled')->update(['value' => '1']);
        $user = User::factory()->unverified()->create(['role' => 'student']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('verification.notice'));
    }
}
