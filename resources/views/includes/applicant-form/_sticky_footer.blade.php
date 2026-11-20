@php
    use App\Models\Application;
    use App\Models\Section;
@endphp

<div class="block md:hidden sticky bottom-0 z-50 bg-white border-b-4 border-zinc-100 shadow-[0px_-4px_4px_0px_rgba(0,0,0,0.13)]">
    <div class="w-full inline-flex flex-col justify-center gap-3 p-4 text-neutral-900">
        <div class="self-stretch inline-flex justify-between items-center text-sm">
            <div class="text-center justify-start">
                <span>@lang('main.sum'):</span>
                <span class="font-bold">{{ $application['amount'] }} €</span>
            </div>
            <div class="text-center justify-start">
                <span>@lang('main.term'):</span>
                <span class="font-bold">{{ $application['term_months'] }} @lang('main.month')</span>
            </div>
            <a @click="formEditModalClosed = false" class="inline-flex flex-col justify-center items-start cursor-pointer">
                <div class="text-center justify-start text-loan-normal underline underline-offset-2">@lang('main.edit')</div>
            </a>
        </div>
        <div class="flex justify-center items-center gap-0.5 md:gap-4">
            @if ($step < Application::MAX_STEPS && !empty($go_back_link))
                {{-- Go back button --}}
                <a href="{{ $go_back_link }}"
                   class="flex applicant-form__back-button px-5"
                   id="back_button"
                   type="button">
                    <div class="w-full grid grid-cols-[1fr_10fr]">
                        <svg class="h-5 mt-1"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                        @lang('main.back')
                    </div>
                </a>

                {{-- Submit button --}}
                <button
                    id="submit_button"
                    type="submit"
                    form="applicant_form"
                    class="applicant-form__button text-lg disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                >
                    <div class="w-full grid grid-cols-[10fr_1fr]">
                        @if($section['route_name'] == 'step2')
                            @lang('main.confirm')
                        @else
                            @lang('Continue')
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="h-5 mt-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </div>
                    @include('includes.loading-spinner')
                </button>

            @else
                {{-- Submit button --}}
                <button
                    id="submit_button"
                    type="submit"
                    form="applicant_form"
                    class="applicant-form__button text-lg disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                >
                    @lang('Continue')
                    @include('includes.loading-spinner')
                </button>
            @endif
        </div>
    </div>
</div>
