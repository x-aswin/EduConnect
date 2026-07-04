<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->id,
                'hash' => sha1($this->email),
            ]
        );

        Log::channel('stderr')->info("Verification link for {$this->email}: {$verificationUrl}");

        $this->notify(new CustomVerifyEmail($verificationUrl));
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        Log::channel('stderr')->info("Password reset link for {$this->email}: {$resetUrl}");

        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Link to the student profile
     */
    public function student(): HasOne{
        return $this->hasOne(Student::class);
    }
    /**
     * Link to the college profile
     */
    public function college(): HasOne
    {
        return $this->hasOne(College::class);
    }

    /**
     * Link to the Firm profile
     */
    public function firm(): HasOne
    {
        return $this->hasOne(Firm::class);
    }

    /**
     * Link to the Mentor profile
     */
    public function mentor(): HasOne
    {
        return $this->hasOne(Mentor::class);
    }
    public function enrollments():HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function chatsAsStudent(): HasMany
    {
        return $this->hasMany(Chat::class, 'student_id');
    }

    public function chatsAsMentor(): HasMany
    {
        return $this->hasMany(Chat::class, 'mentor_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
