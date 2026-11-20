<?php

namespace App\Models\Lender;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $campaign_name
 * @property $clicks
 * @property $average_epc
 */
class LenderSorting extends Model
{
    protected $guarded = [];
    protected $table = 'lenders__sorting';
}
