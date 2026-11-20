<?php

namespace App\Filters\RuleSets;

use App\Models\Lender;

class ApplyFrequency implements RuleSet
{
    /**
     * @param array $rules
     * @param mixed $lenders
     * @return array|object
     */
    public function apply(array $rules, $lenders)
    {
        if (!empty($rules['set_campaigns_frequencies'])) {
            $lenders = $this->setFrequency($rules['set_campaigns_frequencies'], $lenders);

            $lenders = $this->applyFrequency($lenders);
        }

        return $lenders;
    }

    /**
     * @param mixed $campaigns_frequencies
     * @param mixed $lenders
     * @return array
     */
    protected function setFrequency($campaigns_frequencies, $lenders)
    {
        if (!is_array($campaigns_frequencies)) {
            $campaigns_frequencies = (array) $campaigns_frequencies;
        }

        if (is_array($lenders)) {
            return array_map(function (Lender $lender) use ($campaigns_frequencies) {
                if (!empty($campaigns_frequencies[$lender->campaign_id])) {
                    $lender->frequency = (int) $campaigns_frequencies[$lender->campaign_id];
                }

                return $lender;
            }, $lenders);
        }

        return $lenders->map(function (Lender $lender) use ($campaigns_frequencies) {
            if (!empty($campaigns_frequencies[$lender->campaign_id])) {
                $lender->frequency = (int) $campaigns_frequencies[$lender->campaign_id];
            }

            return $lender;
        });
    }

    protected function applyFrequency($lenders)
    {
        if (is_array($lenders)) {
            foreach ($lenders as $lender_key => $lender) {
                if ($lender->frequency) {
                    if (rand(1, $lender->frequency) === $lender->frequency) {
                        // keep Lender in $lenders array
                    } else {
                        unset($lenders[$lender_key]);
                    }
                }
            }
        } else {
            foreach ($lenders as $lender_key => $lender) {
                if ($lender->frequency) {
                    if (rand(1, $lender->frequency) === $lender->frequency) {
                        // keep Lender in $lenders array
                    } else {
                        $lenders->forget($lender_key);
                    }
                }
            }
        }

        return $lenders;
    }
}
