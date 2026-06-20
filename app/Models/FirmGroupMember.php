<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group_id', 'name', 'contact_info'])]
class FirmGroupMember extends Model
{
    public function group(): BelongsTo
    {
        return $this->belongsTo(FirmGroup::class, 'group_id');
    }
}
