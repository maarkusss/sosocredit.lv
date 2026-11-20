@php
    /** @var int $current_type_id */
    /** @var array $applicant_form_types */
    $icon = '';
    $current_loantype = collect($applicant_form_types)->filter(function($array) use ($current_type_id) {
        return $array['id'] == $current_type_id;
    })->first();

    if (!empty($current_loantype) && !empty($current_loantype['icon'])) {
        $icon = $current_loantype['icon'];
    }
@endphp

<div class="w-full" x-data="{ selectedName: '{{ $current_loantype['name'] ?? '' }}' }">
    <div
        class="flex bg-green-700 rounded-t-lg flex-row justify-between items-center w-full py-4 text-base md:text-lg font-bold text-white cursor-pointer"
        id="applicantFormDropdown"
        type="button">

    <span class="inline-flex flex-row justify-start items-center ml-4">
        @if (!empty($current_type_id))
            @if ($current_type_id === 1 || (app()->getLocale() === 'ru' && $current_type_id === 2))
                @include('includes.svgs.shopping-bag')

                <span class="ml-3">
                   @lang('Consumer loan')
                </span>
            @else
                <span class="ml-3" x-text="selectedName || '@lang('applicant_form.consumer_loan')'">
                    {{ $current_loantype['name'] ?? __('Consumer loan') }}
                </span>
            @endif
        @endif
    </span>

        <svg class="w-4 h-4 mr-5"
             xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <ul class="bg-white rounded-b-lg shadow-lg hidden w-full absolute top-16 left-0 z-10"
        id="applicantFormDropdownList"
    >
        @foreach ($applicant_form_types as $key => $applicant_form_type)
            @if (!empty($current_type_id) && $current_type_id != $applicant_form_type['id'])
                <li class="flex justify-center items-center w-full pl-3 text-base md:text-lg hover:bg-[#EBF4EC] @if($loop->last) rounded-b-lg @endif">
                    @if(Route::currentRouteName() === 'consumers.dashboard')
                        <span class="w-full px-3 py-3 text-base font-bold text-[#081B1A] cursor-pointer"
                              @click="
                                selectedName = '{{ $applicant_form_type['name'] }}';
                                selectedUrl = '{{ $applicant_form_type['url'] }}';
                                selectedSectionId = '{{ $applicant_form_type['id'] }}';
                                selectedLoanType = '{{ $applicant_form_type['name'] }}';
                                ">{{ $applicant_form_type['name'] }}
                        </span>
                    @else
                        <a class="w-full px-3 py-3 text-base font-bold text-[#081B1A]"
                           href="{{ $applicant_form_type['url'] }}">
                            {{ $applicant_form_type['name'] }}
                        </a>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</div>
