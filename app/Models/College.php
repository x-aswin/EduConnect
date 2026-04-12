<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 
    'institution_name', 
    'photo', 
    'college_phone', 
    'address', 
    'website', 
    'contact_person', 
    'designation', 
    'contact_number', 
    'verification_doc'
])]
class College extends Model
{
    /**
     * Get the user that owns the college profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function mentors(): HasMany
    {
        return $this->hasMany(Mentor::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
