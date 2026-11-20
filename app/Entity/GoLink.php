<?php

namespace App\Entity;

use App\Models\GoRewrite;
use App\Models\Lender;

/**
 * GoLink Entity
 *
 * Used to generate external links on Offer page for the "go" route
 */
class GoLink
{
    public ?UtmDataDTO $utm_data;

    public const REQUIRED_PARAMS = [
        's1',
        's2',
        's3',
        's4',
        's5',
    ];

    public function __construct()
    {
        $this->utm_data = UtmDataDTO::fromRequest();
    }

    public function build(Lender $lender, int $position = 0, ?GoRewrite $go_rewrite = null, $user = null): string
    {
        $link = $lender->affiliate_link;

        $params = [
            's1' => s1(),
            's2' => $this->utm_data->utm_campaign,
            's3' => $this->utm_data->gclid,
            's4' => $this->utm_data->utm_content,
            's5' => $this->utm_data->utm_source,
            's6' => $position,
            's7' => $this->utm_data->s7,
            's8' => $this->utm_data->s8 || $this->utm_data->s8 === '0' ? $this->utm_data->s8 : null,
            's9' => $user?->phone ? (phone_extension() . $user->phone) : '',
            's10' => $user?->email ?? '',
        ];

        if (config('app.go_rewrites_enabled') === true) {
            if (empty($go_rewrite)) {
                $go_rewrites = GoRewrite::getCachedRewrites();
                if (!empty($go_rewrites)) {
                    /** @var GoRewrite $go_rewrite */
                    foreach ($go_rewrites as $go_rewrite) {
                        if ($go_rewrite->matchesParams($params)) {
                            $link = $go_rewrite->rewriteDomain($link);
                            $link = $go_rewrite->rewritePathEnding($link);
                            $params = $go_rewrite->rewriteParams($params);
                        }
                    }
                }
            } elseif ($go_rewrite->matchesParams($params)) {
                $link = $go_rewrite->rewriteDomain($link);
                $link = $go_rewrite->rewritePathEnding($link);
                $params = $go_rewrite->rewriteParams($params);
            }
        }

        $parsed_link = parse_url($link);

        /**
         * If Lender's affiliate link has its own params -
         * prioritise added s1,s2,s3,s4,s5,s6 params over whatever is set in the affiliate link
         */
        if (!empty($parsed_link['query'])) {
            $link_query_params = [];
            $exploded_query = explode('&', $parsed_link['query']);

            foreach ($exploded_query as $param) {
                $exploded_param = explode('=', $param, 2);
                $key = $exploded_param[0];
                $value = $exploded_param[1];

                $link_query_params[$key] = $value;
            }

            if (!empty($link_query_params)) {
                $link = $parsed_link['scheme'] . '://' . $parsed_link['host'] . $parsed_link['path'];

                $params = array_merge($link_query_params, $params);
            }
        }

        $params = http_build_query($params);

        $link .= '?' . $params;

        return $link;
    }
}
