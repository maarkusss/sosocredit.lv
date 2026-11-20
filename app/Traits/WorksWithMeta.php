<?php

namespace App\Traits;

use App\Models\Section;
use App\Models\Setting;
use App\Contracts\Seoable;

trait WorksWithMeta
{
    /**
     * @deprecated
     */
    protected function metaFromSection(Section $section): array
    {
        $site_name = $this->seoSiteName();

        return [
            'title' => $section->title . ' - ' . $site_name,
            'description' => $section->description,
            'keywords' => $section->keywords,
        ];
    }

    protected function metaTitle(string $title, bool $add_sitename = true): array
    {
        if ($add_sitename === true) {
            $title .= ' - ' . $this->seoSiteName();
        }

        return [
            'title' => $title,
        ];
    }

    protected function seoSiteName(): string
    {
        $site_name = config('app.name');
        $settings_name = Setting::retrieve('name');
        if ($settings_name) {
            $site_name = $settings_name;
        }

        return $site_name;
    }

    protected function metaFromSeoable(Seoable $section): array
    {
        $site_name = $this->seoSiteName();
        $SEPARATOR = '-';

        $meta_title = $section->name;
        if (!empty($section->title)) {
            $meta_title = $section->title;
        }

        if (!str_contains($meta_title, $site_name)) {
            $meta_title .= ' ' . $SEPARATOR . ' ' . $site_name;
        }

        $meta_description = $meta_title;
        if (!empty($section->description)) {
            $meta_description = $section->description;
        }

        return [
            'title' => $meta_title,
            'description' => $meta_description,
            'keywords' => $section->keywords ?? '',
        ];
    }
}
