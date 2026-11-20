<?php

namespace App\Filters\RuleSets;

use App\Models\Lender;

class ExcludeLenders implements RuleSet
{
    /**
     * @param array $rules
     * @param mixed $lenders
     * @return array|object
     */
    public function apply(array $rules, $lenders)
    {
        // by id (old, not as universal)
        if (!empty($rules['exclude'])) {

            if (is_array($lenders)) {
                $lenders = array_filter($lenders, function (Lender $lender) use ($rules) {
                    return !in_array($lender->id, $rules['exclude']);
                });
            } else {
                $lenders = $lenders->filter(function (Lender $lender) use ($rules) {
                    return !in_array($lender->id, (array) $rules['exclude']);
                });
            }

        }

        // by campaign_id
        if (!empty($rules['exclude_campaigns'])) {

            if (is_array($lenders)) {
                $lenders = array_filter($lenders, function (Lender $lender) use ($rules) {
                    return !in_array($lender->campaign_id, (array)$rules['exclude_campaigns']);
                });
            } else {
                $lenders = $lenders->filter(function (Lender $lender) use ($rules) {
                    return !in_array($lender->campaign_id, (array) $rules['exclude_campaigns']);
                });
            }
        }

        return $lenders;
    }
}
