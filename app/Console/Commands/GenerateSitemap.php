<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generates the sitemap, saves it into public/sitemap.xml';

    public function handle()
    {
        $sitemapLink = Setting::retrieve('sitemap');

        if (!$sitemapLink) {
            return;
        }

        $excludedPaths = [
            '',
            '/',
            '/lv/cookies',
            '/lv/privatuma-politika',
            '/lv/lietosana',
            '/lv/push',
            '/lv/unsubscribe/email',
            '/lv/unsubscribe/phone',
            '/lv/unsubscribe/rc',
            '/ru/zashchita-dannih',
            '/ru/unsubscribe/email',
            '/ru/unsubscribe/phone',
            '/ru/unsubscribe/rc',
            '/ru/cookies',
            '/ru/push',
            '/ru/ispolzovanie',
        ];

        SitemapGenerator::create($sitemapLink)
            ->hasCrawled(function (Url $url) use ($excludedPaths) {

                $parsedUrl = parse_url($url->url);

                if (($parsedUrl['scheme'] ?? '') === 'http') {
                    return;
                }

                if (!empty($parsedUrl['query'])) {
                    return;
                }

                $path = $parsedUrl['path'] ?? '/';
                $path = rtrim($path, '/');

                if (in_array($path, $excludedPaths, true)) {
                    return;
                }

                if (str_contains($url->url, '%C5%A1')) {
                    return;
                }

                return $url;
            })
            ->writeToFile(public_path('sitemap.xml'));
    }
}
