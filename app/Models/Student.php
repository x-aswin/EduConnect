<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'full_name', 'phone', 'dob', 'gender', 'current_qualification', 'address','photo'])]
class Student extends Model
{
    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }
    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
