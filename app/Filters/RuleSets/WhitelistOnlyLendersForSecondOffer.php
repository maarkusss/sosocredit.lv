<?php

namespace App\Filters\RuleSets;

use App\Models\Lender;

class WhitelistOnlyLendersForSecondOffer implements RuleSet
{
    public function apply(array $rules, $lenders): array
    {
        if (!empty(config('app.second_lender_offer_include_campaign_ids'))) {
            if (is_array($lenders)) {
                $whitelist_only_lenders = array_filter($lenders, fn (Lender $lender) => in_array($lender->campaign_id, config('app.second_lender_offer_include_campaign_ids')));
            } else {
                $whitelist_only_lenders = $lenders->filter(function (Lender $lender) use ($rules) {
                    return in_array($lender->campaign_id, config('app.second_lender_offer_include_campaign_ids'));
                });
            }

            if (!empty($whitelist_only_lenders)) {
                $lenders = $whitelist_only_lenders;
            }
        }

        return $lenders;
    }
}
