<?php

namespace App\Models\Lender;

use App\Models\Lender;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToLender
{
    public function lender(): BelongsTo
    {
        return $this->belongsTo(Lender::class, 'lender_id');
    }
}
