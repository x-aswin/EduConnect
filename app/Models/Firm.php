<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 
    'org_name', 
    'org_type', 
    'photo',
    'contact_person', 
    'designation', 
    'phone', 
    'verification_doc', 
    'address'
])]
class Firm extends Model
{
    /**
     * Get the user that owns the firm profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(FirmGroup::class);
    }
}
