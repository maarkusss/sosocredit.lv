<?php

namespace App\Traits;

use App\Models\Section;

trait WorksWithHeaderMenu
{
    protected function getHeaderMenu(): array
    {
        return [];
        $data = [];
        $locale = app()->getLocale();
        $header_menu_items = Section::active()
            ->where('lang', $locale)
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->limit(1)
            ->pluck('name', 'route_name')
            ->toArray();

        if (!empty($header_menu_items)) {
            $data['/' . $locale] = __('Loan comparison');

            foreach ($header_menu_items as $route_name => $name) {
                $data['/' . $locale . '/' . $route_name] = $name;
            }
        }

        return $data;
    }
}
