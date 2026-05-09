<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'dob',
        'gender',
        'current_qualification',
        'address',
        'photo',
    ];

    protected $casts = [
        'dob' => 'date',
    ];
    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
