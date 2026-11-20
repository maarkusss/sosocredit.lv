<?php

namespace App\Models\Consumer;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $consumer_id
 * @property $hoi_token
 * @property $clicked
 * @property $clicked_date
 * @property $created_at
 * @property $updated_at
 */
class ConsumersHoiToken extends Model
{
    use BelongsToConsumer;

    protected $guarded = [];
    protected $table = 'consumers__hoi_token';

    public function touchClickedDate(): void
    {
        $this->update([
            'clicked' => 1,
            'clicked_date' => date('Y-m-d H:i:s'),
        ]);
    }
}
