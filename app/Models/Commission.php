<?php

namespace App\Models;

use App\Services\GoodAff\GoodAffStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $id
 * @property $visitor_id
 * @property $uid
 * @property $transaction_id
 * @property $commission
 * @property $lender_id
 * @property $referrer
 * @property $ip
 * @property $status
 * @property $utm_campaign
 * @property $utm_source
 * @property $gclid
 * @property $s4
 * @property $s7
 * @property $consumer_id
 * @property $commission_date
 * @property $created_at
 * @property $updated_at
 *
 * @method approved()
 */
class Commission extends Model
{
    protected $guarded = [];
    protected $table = 'commissions';

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', GoodAffStatus::APPROVED);
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(Lender::class, 'lender_id');
    }
}
