<?php

namespace App\Services;

use App\Contracts\OfferServiceInterface;
use App\Entity\UtmDataDTO;
use App\Models\Lender;
use App\Models\Lender\LenderData;
use Illuminate\Support\Str;

class LenderService implements OfferServiceInterface
{
    public function getAll($locale = ''): array
    {
        if (empty($locale)) {
            $locale = default_locale();
        }

        $utm_data = UtmDataDTO::fromAppRequest();

        $PREFIX = s1();
        $DEFAULT_ZERO_PERCENT_VALUE = api_default_zero_percent_value();

        $lenders = Lender::active()
            ->select([
                'id',
                'name',
                'image',
                'affiliate_link',
                'frequency',
                'first_loan',
                'max_amount',
                'min_years',
                'max_years',
                'receiving_time',
                'zero_percent',
                'zero_percent_value',
                'campaign_id',
            ])
            ->orderBy('position', 'ASC')
            ->get();

        $lenders = Lender::filter($lenders, $utm_data);

        $lenders = collect($lenders);

        $lender_arr = [];
        if ($lenders->isNotEmpty()) {
            $i = 0;
            $position = 0;
            foreach ($lenders as $lender) {
                if ($lender->frequency) {
                    if (rand(1, $lender->frequency) == $lender->frequency) {
                        // all good, keep adding $lender into $lender_arr
                    } else {
                        // skip lender if it has not-null $lender->frequency and does not match the condition
                        continue;
                    }
                }

                $position++;

                $lendersdata = LenderData::query()
                    ->where('lender_id', $lender->id)
                    ->where('lang', $locale)
                    ->select([
                        'slogan',
                    ])
                    ->first();

                if ($lender->image) {
                    $lender->image = config('app.url') . $lender->image;
                }

                if (!empty($PREFIX)) {
                    $lender->affiliate_link = $lender->affiliate_link . "?s1=" . $PREFIX;
                }

                if (!empty(config('offers-api.go_link'))) {
                    $affiliate_link = config('offers-api.go_link') . $lender->id . '/' . $position;
                    $affiliate_link = str_replace('/api/go/', '/' . default_locale() . '/go/', $affiliate_link);

                    if (!Str::contains($affiliate_link, '?s1=')) {
                        $affiliate_link .= '?s1=' . s1();
                    }

                    $lender->affiliate_link = $affiliate_link;
                }

                if (!empty($lender->zero_percent) && empty($lender->zero_percent_value)) {
                    $lender->zero_percent_value = $DEFAULT_ZERO_PERCENT_VALUE ?? '';
                }

                $lender_arr[$i] = $lender;
                $lender_arr[$i]['slogan'] = $lendersdata?->slogan;
                $i++;
            }
        }

        return $lender_arr;
    }
}
