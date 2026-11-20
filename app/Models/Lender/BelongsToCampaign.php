<?php

namespace App\Models\Lender;

use App\Models\LenderCampaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $campaign_id
 */
trait BelongsToCampaign
{
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(LenderCampaign::class, 'campaign_id');
    }
}
