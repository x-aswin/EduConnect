<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'course_id',
        'user_id',
        'type',
        'status',
        'requested_venue',
        'proposed_start',
        'proposed_end',
        'proposed_time',
        'payment_status',
        'participant_count',
        'total_amount',
        'college_note',
])]
class Enrollment extends Model
{
    // The course this enrollment belongs to
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // The user (student or firm) who enrolled
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Participants — only for firm enrollments
    public function participants(): HasMany
    {
        return $this->hasMany(FirmParticipant::class);
    }
}
