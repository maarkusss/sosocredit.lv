@php
    use App\Models\Application;
    use App\Models\Section;

    /** @var Application $application */
    /** @var array $applicant_form_types */
    /** @var Section $section */

    $is_business = false;
    $is_mortgage = false;
    $is_car_mortgage = false;
    $section_id = $section->parent_section_id ?? '';
    $loantype = $section->parent?->name ?? '';

    $current_loantype = collect($applicant_form_types)->filter(function($array) use ($section) {
        return $array['id'] == $section->parent_section_id;
    })->first();

    if (!empty($current_loantype)) {
        $is_business = !empty($current_loantype['is_business']);
        $is_mortgage = !empty($current_loantype['is_mortgage']);
        $is_car_mortgage = !empty($current_loantype['is_car_mortgage']);
        $loantype = $current_loantype['name'];
    }
@endphp

@extends('layouts.public')

@section('content')
    @if (config('app.name') === 'Sosocredit.lv' && $head_code_go = \App\Models\Setting::retrieve('head_code_go'))
        {!! $head_code_go !!}
    @endif

    <main>
        <section class="w-full bg-loan-normal px-3 flex justify-center" id="slogan-top" style="height: 1px;"></section>
        <section class="section py-5 sm:py-8">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">
                    @if (!empty($section->h1_description))
                        <div class="section__content__hero">

                            <div class="mt-4 w-full text-base leading-5 text-center">
                                {{ $section->h1_description }}
                            </div>
                        </div>
                    @endif
                    <div class="landing-form-wrapper applicant-form">
                        <div class="w-full" id="applicant_form">
                            @csrf

                            <input type="hidden" id="locale" value="{{ app()->getLocale() }}"/>

                            <div class="landing-form__steps">
                                <div class="applicant-form__wrapper">
                                    <div>
{{--                                        <h2 class="text-base text-gray-500 border-b border-gray-300 pb-4">@lang('Loan application is accepted')</h2>--}}

                                        <div class="mb-5">
                                            @include('includes.applicant-form._breadcrumbs', [
                                                'step' => 5,
                                                'title' => ' ',
                                                'hide_step4' => $is_business,
                                            ])
                                        </div>

                                        <div class="mt-10 text-center max-w-sm mx-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5"
                                                 stroke="#3faf4f"
                                                 class="w-10 h-10 mx-auto mb-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>

                                            <p class="font-bold text-lg">
                                                @lang('applicant_form.success_message')
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Submit button --}}
                                    <a href="{{ !empty($redirect_url) ? $redirect_url : '/' . app()->getLocale() }}"
                                        class="applicant-form__button text-lg mt-8 disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                                        type="button">
                                        @lang('Close')
                                    </a>
                                </div>
                            </div>

                            <p class="applicant-form__footer">
                                * @lang('applicant_form.during_working_hours')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
