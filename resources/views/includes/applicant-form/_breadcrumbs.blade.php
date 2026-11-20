@php
    use App\Models\Application;

    if (empty($max_steps)) {
        $max_steps = Application::MAX_STEPS;
    }

    if (empty($title)) {
        $title = __('applicant_form.application');
    }

    $progress_width = 0;

    if ($step === 1) {
        $progress_width = '20';
    } elseif ($step === 2) {
        $progress_width = '40';
    } elseif ($step === 3) {
        $progress_width = '60';
    } elseif ($step === 4) {
        $progress_width = '80';
    } elseif ($step > 4) {
        $progress_width = '100';
    }
@endphp

{{-- Percentage line --}}
{{--<div class="mb-4">--}}
{{--    <div class="w-full bg-[#eaf3da] rounded-full h-2">--}}
{{--        <div--}}
{{--            class="bg-[#3faf4f] h-2 rounded-full"--}}
{{--            style="width: {{ $progress_width }}%">--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <p class="text-gray-500 mt-1 text-sm">--}}
{{--        {{ __('Completed on') }} {{ $progress_width }}%--}}
{{--    </p>--}}
{{--</div>--}}

{{-- Separate steps --}}
{{--<div class="mt-4">--}}

{{--    @if ($step < 3 && !empty($go_back_link))--}}
{{--        <div class="flex items-center justify-start mb-4">--}}
{{--            <a--}}
{{--                href="{{ $go_back_link }}"--}}
{{--                class="applicant-form__back-button"--}}
{{--                id="back_button"--}}
{{--                type="button">--}}
{{--                <svg class="w-4 h-4 mr-1"--}}
{{--                     xmlns="http://www.w3.org/2000/svg"--}}
{{--                     fill="none"--}}
{{--                     viewBox="0 0 24 24"--}}
{{--                     stroke="currentColor">--}}
{{--                    <path stroke-linecap="round"--}}
{{--                          stroke-linejoin="round"--}}
{{--                          stroke-width="2"--}}
{{--                          d="M15 19l-7-7 7-7"/>--}}
{{--                </svg>--}}
{{--                @lang('main.back')--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    <div class="w-full max-w-screen-md mb-8">--}}
{{--        <div class="applicant-form__breadcrumbs">--}}
{{--            @if (empty($hide_step2))--}}

{{--                <div class="applicant-form__breadcrumb">--}}
{{--                    <span--}}
{{--                        class="@if ($step > 0) applicant-form__breadcrumb__title_active @else applicant-form__breadcrumb__title_inactive @endif">--}}
{{--                        <span class="hidden sm:inline">1. </span>--}}
{{--                        @lang('applicant_form.information')--}}
{{--                    </span>--}}
{{--                    <div--}}
{{--                        class="@if ($step > 0) applicant-form__breadcrumb__dot_active @else applicant-form__breadcrumb__dot_inactive @endif hidden sm:block"></div>--}}
{{--                </div>--}}

{{--                <div class="applicant-form__breadcrumb">--}}
{{--                    <span--}}
{{--                        class="@if ($step > 1) applicant-form__breadcrumb__title_active @else applicant-form__breadcrumb__title_inactive @endif">--}}
{{--                        <span class="hidden sm:inline">2. </span>--}}
{{--                        @lang('applicant_form.bank_note_short')--}}
{{--                    </span>--}}
{{--                    <div--}}
{{--                        class="@if ($step > 1) applicant-form__breadcrumb__dot_active @else applicant-form__breadcrumb__dot_inactive @endif hidden sm:block"></div>--}}
{{--                </div>--}}

{{--                <div class="applicant-form__breadcrumb">--}}
{{--                <span--}}
{{--                    class="@if ($step > 2) applicant-form__breadcrumb__title_active @else applicant-form__breadcrumb__title_inactive @endif">--}}
{{--                    <span class="hidden sm:inline">3. </span>--}}
{{--                    @lang('applicant_form.response')--}}
{{--                </span>--}}
{{--                    <div--}}
{{--                        class="@if ($step > 2) applicant-form__breadcrumb__dot_active @else applicant-form__breadcrumb__dot_inactive @endif hidden sm:block"></div>--}}
{{--                </div>--}}

{{--            @else--}}

{{--                <div class="applicant-form__breadcrumb">--}}
{{--                    <span--}}
{{--                        class="@if ($step > 0) applicant-form__breadcrumb__title_active @else applicant-form__breadcrumb__title_inactive @endif">--}}
{{--                        <span class="hidden sm:inline">1. </span>--}}
{{--                        @lang('applicant_form.information')--}}
{{--                    </span>--}}
{{--                    <div--}}
{{--                        class="@if ($step > 0) applicant-form__breadcrumb__dot_active @else applicant-form__breadcrumb__dot_inactive @endif hidden sm:block"></div>--}}
{{--                </div>--}}

{{--                <div class="applicant-form__breadcrumb">--}}
{{--                    <span--}}
{{--                        class="@if ($step > 2) applicant-form__breadcrumb__title_active @else applicant-form__breadcrumb__title_inactive @endif">--}}
{{--                        <span class="hidden sm:inline">2. </span>--}}
{{--                        @lang('applicant_form.response')--}}
{{--                    </span>--}}
{{--                    <div--}}
{{--                        class="@if ($step > 2) applicant-form__breadcrumb__dot_active @else applicant-form__breadcrumb__dot_inactive @endif hidden sm:block"></div>--}}
{{--                </div>--}}

{{--            @endif--}}

{{--        </div>--}}
{{--        <div class="applicant-form__breadcrumb__bgline">--}}
{{--            <div class="h-full rounded-full bg-loan-normal"--}}
{{--                 style="width: @if ($step > 2) 100% @elseif ($step > 1) 50% @elseif (!empty($hide_step2)) 25% @else 17%; @endif"></div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="mt-4 flex flex-row justify-between">
    <h3 class="text-xl text-neutral-900 font-bold leading-relaxed md:leading-7">{{ $title }}</h3>
    <p><strong>{{ $step }}</strong> @lang('/') <strong>{{ ($max_steps - 1) }}</strong></p>
</div>
<div class="flex flex-row justify-center mt-4">
    @for ($i = 1; $i <= ($max_steps - 1); $i++)
        <div class="w-full h-1.5 mx-1 rounded-lg @if ($i <= $step) bg-[#3FAF4F] @else bg-zinc-500/20 @endif">
            &nbsp;
        </div>
    @endfor
</div>
