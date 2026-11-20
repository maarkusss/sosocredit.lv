@php
    use App\Models\Application;
    use App\Models\Section;

    /** @var Application $application */
    /** @var array $applicant_form_types */
    /** @var Section $section */
    /** @var bool $disable_amount */
    /** @var bool $disable_term_months */

    $is_business = false;
    $section_id = $section->id ?? '';
    $loantype = $section->name ?? '';
    $current_loantype = collect($applicant_form_types)
        ->filter(function ($array) use ($section) {
            return $array['id'] == $section->id;
        })
        ->first();
    $form_action = '';

    if (!empty($current_loantype)) {
        $is_business = !empty($current_loantype['is_business']);
        $loantype = $current_loantype['name'];
    }

    if ($section->isHome()) {
        $default_loantype_section = Section::query()
            ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
            ->where('lang', app()->getLocale())
            ->orderBy('order', 'ASC')
            ->select(['id', 'name', 'route_name'])
            ->first();

        $section_id = $default_loantype_section->id;
        $loantype = $default_loantype_section->name;
        $form_action = '/' . app()->getLocale() . '/' . $default_loantype_section->route_name;
    }
@endphp

<div class="flex flex-col flex-shrink-0 w-full applicant-form">
    <form action="{{ $form_action }}" method="POST" class="w-full" id="applicant_form">
        @csrf
        <input type="hidden" name="section_id" value="{{ $section_id ?? '' }}" />
        <input type="hidden" name="loantype" value="{{ $loantype ?? '' }}" />

        <div class="grid grid-cols-1 gap-4 mt-4 mb-2 md:mb-10 md:grid-cols-2 md:mt-10">

            <div class="applicant-form__left lg:mr-16">
                <div class="flex justify-start md:justify-center">
                    <div class="text-xl font-bold leading-7 sm:text-2xl md:text-3xl sm:leading-9">
                        <span class="bg-loan-dark px-1 py-0.5 rounded text-white box-decoration-clone">
                            {{ $section->h1_tag() }}
                        </span>
                    </div>
                </div>

                @if (!empty($section->json) && !empty($section->json['block0_options']))
                    <ul class="mt-4 sm:mt-6 sm:mb-4">
                        @foreach ($section->json['block0_options'] as $block0_option)
                            @if (!empty($block0_option))
                                <li
                                    class="flex flex-row items-center justify-start mb-1 text-sm leading-tight text-left text-neutral-900 sm:text-base sm:leading-7 md:mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="3" stroke="#3faf4f" class="w-4 h-4 mr-2 md:w-5 md:h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    {{ $block0_option }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif

                @if (optional($google_stats)->overall_rating)
                    <div class="hidden md:block">
                        <a href="https://www.google.com/maps/place/Sosocredit.lv/@56.6653565,21.8825189,10z/data=!4m10!1m2!2m1!1ssosocredit.lv!3m6!1s0x46ef9929cf7f25f7:0xcae769f72a6bbea7!8m2!3d56.6653565!4d22.4922601!15sCg1zb3NvY3JlZGl0Lmx2kgEOZmluYW5jZV9icm9rZXLgAQA!16s%2Fg%2F11z4sx4t_v?entry=ttu&g_ep=EgoyMDI2MDQwOC4wIKXMDSoASAFQAw%3D%3D"
                            target="_blank" rel="nofollow"
                            class="px-3 py-1.5 font-bold bg-white shadow-[0_1px_4px_0_rgba(0,0,0,0.15)] md:w-max w-full rounded-2xl flex md:items-center items-start gap-x-1.5 text-[24px]">
                            <div class="p-[2px] md:mt-0 mt-1 bg-[#EEEEEE] rounded-full">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M22.3585 10.0305H17.9514H12.2316V14.2012H17.8691C17.3617 15.5556 16.3983 16.6869 15.1656 17.4098C14.2371 17.955 13.155 18.2672 11.9999 18.2672C9.31263 18.2672 7.02039 16.5757 6.13005 14.1995C5.87284 13.5153 5.73269 12.7739 5.73269 11.9999C5.73269 11.2868 5.85198 10.6016 6.07098 9.96323C6.9167 7.50187 9.25183 5.73276 11.9999 5.73276C13.2169 5.73276 14.3529 6.07973 15.3139 6.68048L18.7294 3.88429C16.9035 2.36836 14.558 1.45715 11.9999 1.45715C7.92527 1.45715 4.39052 3.76847 2.63584 7.15148C1.88219 8.60311 1.45703 10.2517 1.45703 12C1.45703 13.8079 1.91177 15.5092 2.71352 16.9963C4.4942 20.2987 7.98494 22.5428 11.9999 22.5428C14.4983 22.5428 16.7934 21.674 18.6002 20.2217C20.4579 18.7283 21.7989 16.618 22.3122 14.2012C22.4634 13.491 22.5427 12.7548 22.5427 12C22.5427 11.3269 22.4796 10.6682 22.3585 10.0305Z"
                                        fill="#FBBB00" />
                                    <path
                                        d="M18.5999 20.2211L18.6006 20.2217C16.7938 21.674 14.4987 22.5429 12.0002 22.5429C7.98529 22.5429 4.49455 20.2988 2.71387 16.9963L6.13045 14.1995C7.02079 16.5757 9.31298 18.2672 12.0003 18.2672C13.1554 18.2672 14.2375 17.955 15.166 17.4099L18.5999 20.2211Z"
                                        fill="#28B446" />
                                    <path
                                        d="M22.3584 10.0305C22.4795 10.6682 22.5426 11.3269 22.5426 12C22.5426 12.7548 22.4633 13.4911 22.3121 14.2013C21.7989 16.618 20.4578 18.7283 18.6001 20.2217L18.5995 20.2211L15.1655 17.4098C16.3982 16.6869 17.3615 15.5556 17.869 14.2013H12.2314V10.0305H17.9513H22.3584Z"
                                        fill="#518EF8" />
                                    <path
                                        d="M18.7292 3.88434L15.3138 6.68053C14.3528 6.07978 13.2169 5.73281 11.9998 5.73281C9.25168 5.73281 6.9166 7.50192 6.07088 9.96328L2.63635 7.15148H2.63574C4.39041 3.76847 7.92512 1.45715 11.9998 1.45715C14.5579 1.45715 16.9034 2.36836 18.7292 3.88434Z"
                                        fill="#FC3F1D" />
                                </svg>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <span class="text-[20px] font-bold">
                                    {{ optional($google_stats)->overall_rating ?? 0 }}
                                </span>

                                <div class="relative flex items-center" style="height:14px">
                                    @include('components.star', [
                                        'color' => '#FFC107',
                                        'size' => '16',
                                    ])
                                </div>

                                <span class="text-sm font-medium">
                                    {{ '(' . $google_stats->ratings_total }} {{ __('google.reviews') . ')' }}
                                </span>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
            <div class="max-w-full applicant-form__right lg:ml-36">
                @include('includes.applicant-form.app-selector')
            </div>
        </div>
        @if (optional($google_stats)->overall_rating)
            <div class="flex items-center justify-center md:hidden">
                <a href="https://www.google.com/maps/place/Sosocredit.lv/@56.6653704,22.4921672,20.19z/data=!4m15!1m7!3m6!1s0x46ef9929cf7f25f7:0xcae769f72a6bbea7!2sSosocredit.lv!8m2!3d56.6653565!4d22.4922601!16s%2Fg%2F11z4sx4t_v!3m6!1s0x46ef9929cf7f25f7:0xcae769f72a6bbea7!8m2!3d56.6653565!4d22.4922601!15sCg1Tb3NvY3JlZGl0Lmx2kgEOZmluYW5jZV9icm9rZXLgAQA!16s%2Fg%2F11z4sx4t_v?entry=ttu&g_ep=EgoyMDI2MDQwOC4wIKXMDSoASAFQAw%3D%3D"
                    target="_blank" rel="nofollow"
                    class="px-2 py-1.5 font-bold bg-white shadow-[0_1px_4px_0_rgba(0,0,0,0.15)] w-max rounded-lg flex md:items-center items-start gap-x-1.5 text-[24px]">
                    <div class="p-[2px] md:mt-0 mt-1 bg-[#EEEEEE] rounded-full">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.3585 10.0305H17.9514H12.2316V14.2012H17.8691C17.3617 15.5556 16.3983 16.6869 15.1656 17.4098C14.2371 17.955 13.155 18.2672 11.9999 18.2672C9.31263 18.2672 7.02039 16.5757 6.13005 14.1995C5.87284 13.5153 5.73269 12.7739 5.73269 11.9999C5.73269 11.2868 5.85198 10.6016 6.07098 9.96323C6.9167 7.50187 9.25183 5.73276 11.9999 5.73276C13.2169 5.73276 14.3529 6.07973 15.3139 6.68048L18.7294 3.88429C16.9035 2.36836 14.558 1.45715 11.9999 1.45715C7.92527 1.45715 4.39052 3.76847 2.63584 7.15148C1.88219 8.60311 1.45703 10.2517 1.45703 12C1.45703 13.8079 1.91177 15.5092 2.71352 16.9963C4.4942 20.2987 7.98494 22.5428 11.9999 22.5428C14.4983 22.5428 16.7934 21.674 18.6002 20.2217C20.4579 18.7283 21.7989 16.618 22.3122 14.2012C22.4634 13.491 22.5427 12.7548 22.5427 12C22.5427 11.3269 22.4796 10.6682 22.3585 10.0305Z"
                                fill="#FBBB00" />
                            <path
                                d="M18.5999 20.2211L18.6006 20.2217C16.7938 21.674 14.4987 22.5429 12.0002 22.5429C7.98529 22.5429 4.49455 20.2988 2.71387 16.9963L6.13045 14.1995C7.02079 16.5757 9.31298 18.2672 12.0003 18.2672C13.1554 18.2672 14.2375 17.955 15.166 17.4099L18.5999 20.2211Z"
                                fill="#28B446" />
                            <path
                                d="M22.3584 10.0305C22.4795 10.6682 22.5426 11.3269 22.5426 12C22.5426 12.7548 22.4633 13.4911 22.3121 14.2013C21.7989 16.618 20.4578 18.7283 18.6001 20.2217L18.5995 20.2211L15.1655 17.4098C16.3982 16.6869 17.3615 15.5556 17.869 14.2013H12.2314V10.0305H17.9513H22.3584Z"
                                fill="#518EF8" />
                            <path
                                d="M18.7292 3.88434L15.3138 6.68053C14.3528 6.07978 13.2169 5.73281 11.9998 5.73281C9.25168 5.73281 6.9166 7.50192 6.07088 9.96328L2.63635 7.15148H2.63574C4.39041 3.76847 7.92512 1.45715 11.9998 1.45715C14.5579 1.45715 16.9034 2.36836 18.7292 3.88434Z"
                                fill="#FC3F1D" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <div class="relative flex items-center" style="height:14px">
                            @include('components.star', [
                                'color' => '#FFC107',
                                'size' => '14',
                            ])
                        </div>

                        <span class="text-base font-bold">
                            {{ optional($google_stats)->overall_rating ?? 0 }}
                        </span>

                        <span class="text-sm font-normal">
                            {{ '(' . $google_stats->ratings_total }} {{ __('google.reviews') . ')' }}
                        </span>
                    </div>
                </a>
            </div>
        @endif
    </form>
</div>
