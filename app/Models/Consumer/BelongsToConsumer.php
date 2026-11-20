<?php

namespace App\Models\Consumer;

use App\Models\Consumer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $consumer_id
 */
trait BelongsToConsumer
{
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class, 'consumer_id');
    }
}
