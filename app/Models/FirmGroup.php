<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['firm_id', 'group_name', 'is_active'])]
class FirmGroup extends Model
{
    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(FirmGroupMember::class, 'group_id');
    }
}
