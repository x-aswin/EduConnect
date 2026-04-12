<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 
    'college_id',  
    'qualification', 
    'expertise', 
    'bio', 
    'photo'
])]
class Mentor extends Model
{
    /**
     * Get the user that owns the mentor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the college this mentor belongs to.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
