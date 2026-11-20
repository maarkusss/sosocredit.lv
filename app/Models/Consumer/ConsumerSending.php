<?php

namespace App\Models\Consumer;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $consumer_id
 * @property $type
 * @property $subject
 * @property $message
 * @property $created_at
 * @property $updated_at
 */
class ConsumerSending extends Model
{
    use BelongsToConsumer;

    protected $table = 'consumers__sendings';

    protected $guarded = [];

    public $timestamps = true;
}
