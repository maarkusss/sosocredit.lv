<?php

namespace App\Filters\RuleSets;

use App\Models\Lender;

class ExcludeLendersFromFirstOffer implements RuleSet
{
    public function apply(array $rules, $lenders): array
    {
        if (!empty(config('app.first_lender_offer_exclude_campaign_ids'))) {
            if (is_array($lenders)) {
                $lenders = array_filter($lenders, fn (Lender $lender) => !in_array($lender->campaign_id, config('app.first_lender_offer_exclude_campaign_ids')));
            } else {
                $lenders = $lenders->filter(function (Lender $lender) use ($rules) {
                    return !in_array($lender->campaign_id, config('app.first_lender_offer_exclude_campaign_ids'));
                });
            }
        }

        return $lenders;
    }
}
