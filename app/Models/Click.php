<?php

namespace App\Models;

use App\Models\Lender\BelongsToLender;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $visitor_id
 * @property $lender_id
 * @property $referer
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $gclid
 * @property $fbp
 * @property $fbc
 * @property $ip
 * @property $country
 * @property $user_agent
 * @property $created_at
 * @property $updated_at
 */
class Click extends Model
{
    use BelongsToLender;

    protected $guarded = [];
    protected $table = 'clicks';
}
