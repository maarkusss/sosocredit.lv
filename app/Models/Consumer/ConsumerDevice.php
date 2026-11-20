<?php

namespace App\Models\Consumer;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $provider
 * @property $key
 * @property $value
 * @property $created_at
 * @property $updated_at
 */
class ConsumerDevice extends Model
{
    use BelongsToConsumer;

    protected $guarded = [];
    protected $table = 'consumers__devices';

    public const PROVIDER_FIREBASE = 'firebase';
}
