<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property $id
 * @property $name
 * @property $campaign_domain
 * @property $active
 *
 * @method active()
 */
class LenderCampaign extends Model
{
    protected $guarded = [];
    protected $table = 'lendercampaigns';
    public $timestamps = false;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function lender(): HasOne
    {
        return $this->hasOne(Lender::class, 'campaign_id', 'id');
    }

    public static function linkToLendersTable(): void
    {
        LenderCampaign::query()
            ->get()
            ->each(function (LenderCampaign $campaign) {
                $explode_name = explode(" - ", $campaign->name);

                $lender = Lender::query()
                    ->where('campaign_id', 0)
                    ->where('name', 'like', $explode_name[0] . '%')
                    ->first();

                if (!empty($lender->id)) {
                    $lender->campaign_id = $campaign->id;
                    $lender->save();
                }
            });
    }
}
