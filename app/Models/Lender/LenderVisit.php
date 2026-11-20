<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $visitor_id
 * @property $utm_campaign
 * @property $bot
 * @property $created_at
 * @property $updated_at
 */
class LenderVisit extends Model
{
    protected $guarded = [];
    protected $table = 'lenders__visits';
}
