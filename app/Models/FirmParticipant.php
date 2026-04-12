<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'enrollment_id',
    'name',
    'contact_info',
])]
class FirmParticipant extends Model
{
     public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
