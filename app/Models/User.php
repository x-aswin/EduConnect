<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
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
}
