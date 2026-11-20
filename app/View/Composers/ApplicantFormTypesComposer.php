<?php

namespace App\View\Composers;

use App\Models\Application;
use Illuminate\View\View;

class ApplicantFormTypesComposer
{
    public function compose(View $view): void
    {
        if (config('app.name') !== 'Sosocredit.lv') {
            $applicant_form_types = [];
        } else {
            $applicant_form_types = Application::getApplicationTypes(app()->getLocale());
        }

        $view->with('applicant_form_types', $applicant_form_types);
    }
}
