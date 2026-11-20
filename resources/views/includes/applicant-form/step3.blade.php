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

    $current_loantype = collect($applicant_form_types)
        ->filter(function ($array) use ($section) {
            return $array['id'] == $section->parent_section_id;
        })
        ->first();

    if (!empty($current_loantype)) {
        $is_business = !empty($current_loantype['is_business']);
        $is_mortgage = !empty($current_loantype['is_mortgage']);
        $is_car_mortgage = !empty($current_loantype['is_car_mortgage']);
        $loantype = $current_loantype['name'];
        $commonName = $current_loantype['common_name'];
    }
@endphp

@extends('layouts.public')

@section('content')
    <script src="https://unpkg.com/@popperjs/core@2" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/tippy.js@6" referrerpolicy="no-referrer"></script>

    <style>
        .input-error {
            border: 2px solid red !important;
        }
    </style>

    <style>
        .tippy-box[data-theme~='loan-info'] {
            background-color: #FFFFFF;
            color: #000000;
            display: flex;
            padding: 16px;
            justify-content: center;
            align-items: center;
            gap: 10px;
            align-self: stretch;
            border-radius: 4px;
            margin: 0 12px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.10);
        }

        .tippy-box[data-theme~='loan-info'] .tippy-arrow {
            color: #FFFFFF;
        }

        .tippy-box[data-theme~='loan-info'] .tippy-content {
            position: relative;
            z-index: 1;
        }

        /* Mobile styles */
        @media screen and (max-width: 768px) {
            .tippy-box[data-theme~='loan-info'] {
                padding: 12px;
                gap: 8px;
                max-width: calc(100vw - 32px);
                margin: 0 24px;
                font-size: 14px;
            }
        }

        @media screen and (max-width: 480px) {
            .tippy-box[data-theme~='loan-info'] {
                padding: 10px;
                gap: 6px;
                max-width: calc(100vw - 24px);
                margin: 0 18px;
                font-size: 13px;
                border-radius: 3px;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .tippy-box[data-theme~='loan-info'] {
                min-height: 44px;
                /* Minimum touch target size */
                padding: 12px 16px;
            }
        }
    </style>

    <main>
        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="py-5 section_narrower md:section sm:py-8">
            <div class="lg:grid lg:grid-cols-[2fr_800px_280px_2fr]">
                <div class="hidden lg:block"></div>
                <div class="section__content">
                    <div class="mb-4">
                        @include('includes.applicant-form._breadcrumbs', [
                            'step' => 3,
                            'title' => '',
                            'hide_step4' => $is_business,
                        ])
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        @if (!empty($section->h1_description))
                            <div class="section__content__hero">
                                <div class="w-full mt-4 text-base leading-5 text-center">
                                    {{ $section->h1_description }}
                                </div>
                            </div>
                        @endif
                        <div class="landing-form-wrapper applicant-form">
                            <div class="w-full">
                                <div class="landing-form__steps">
                                    @include('admincp.includes.infobox')

                                    <form class="grid grid-cols-1 gap-4" id="applicant_form" method="POST" action="?">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" id="locale" value="{{ app()->getLocale() }}" />
                                        <input type="hidden" name="loan_type" id="loan_type" value="{{ $commonName }}" required/>
                                        <div class="applicant-form__wrapper">
                                            <div>
                                                <!-- Applicant inputs -->
                                                <div class="grid grid-cols-1 gap-4 py-4 sm:grid-cols-2">
                                                    @if ($is_business)
                                                        <div class="applicant-form__input-wrap">
                                                            <label for="business_first_name">
                                                                @lang('First name')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="first_name" id="business_first_name"
                                                                value="{{ old('first_name', $application->first_name ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="business_last_name">
                                                                @lang('Last name')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="last_name" id="business_last_name"
                                                                value="{{ old('last_name', $application->last_name ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="business_personal_code">
                                                                @lang('Personal code')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input applyPersonalCodeMask"
                                                                type="text" name="personal_code"
                                                                id="business_personal_code" placeholder="123456-12345"
                                                                value="{{ old('personal_code', $application->personal_code ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="business_company_name">
                                                                @lang('Company Name')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="company_name" id="business_company_name"
                                                                value="{{ old('company_name', $application->company_name ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="business_company_reg_nr">
                                                                @lang('Company Reg. Nr.')
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="company_reg_nr" id="business_company_reg_nr"
                                                                value="{{ old('company_reg_nr', $application->company_reg_nr ?? '') }}" />
                                                        </div>

                                                        {{-- <div class="applicant-form__input-wrap">
                                                        <label for="business_company_contact_person">
                                                            @lang('Company Contact Person')<sup>*</sup>
                                                        </label>
                                                        <input class="applicant-form__input" type="text"
                                                            name="company_contact_person"
                                                            id="business_company_contact_person"
                                                            value="{{ old('company_contact_person', $application->company_contact_person ?? '') }}"
                                                            required />
                                                    </div> --}}
                                                    @else
                                                        <div class="applicant-form__input-wrap">
                                                            <label for="first_name">
                                                                @lang('First name')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="first_name" id="first_name"
                                                                value="{{ old('first_name', $application->first_name ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="last_name">
                                                                @lang('Last name')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="last_name" id="last_name"
                                                                value="{{ old('last_name', $application->last_name ?? '') }}"
                                                                required />
                                                        </div>


                                                        <div class="applicant-form__input-wrap">
                                                            <label for="personal_code">
                                                                @lang('Personal code')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input applyPersonalCodeMask"
                                                                type="text" name="personal_code" id="personal_code"
                                                                placeholder="123456-12345"
                                                                value="{{ old('personal_code', $application->personal_code ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label>@lang('Date of birth')<sup>*</sup></label>

                                                            <input type="hidden" name="age" id="age">

                                                            <div style="display:flex; gap:10px;">

                                                                <!-- Day -->
                                                                <select name="dob_day" class="applicant-form__input"
                                                                    title="DD" required>
                                                                    <option value="">@lang('DD')</option>
                                                                    @for ($i = 1; $i <= 31; $i++)
                                                                        <option value="{{ $i }}"
                                                                            {{ old('dob_day', $application->age ? \Carbon\Carbon::parse($application->age)->format('d') : '') == $i ? 'selected' : '' }}>
                                                                            {{ sprintf('%02d', $i) }}
                                                                        </option>
                                                                    @endfor
                                                                </select>

                                                                <!-- Month -->
                                                                <select name="dob_month" class="applicant-form__input"
                                                                    title="MM" required>
                                                                    <option value="">@lang('MM')</option>
                                                                    @for ($i = 1; $i <= 12; $i++)
                                                                        <option value="{{ $i }}"
                                                                            {{ old('dob_month', $application->age ? \Carbon\Carbon::parse($application->age)->format('m') : '') == $i ? 'selected' : '' }}>
                                                                            {{ sprintf('%02d', $i) }}
                                                                        </option>
                                                                    @endfor
                                                                </select>

                                                                <!-- Year -->
                                                                <select name="dob_year"
                                                                    class="selectpicker applicant-form__input"
                                                                    title="YYYY" required>
                                                                    <option value="">@lang('YYYY')</option>

                                                                    @php
                                                                        $currentYear = now()->year;
                                                                        $maxYear = $currentYear - 18;
                                                                        $minYear = $currentYear - 95;
                                                                        $selectedYear = old(
                                                                            'dob_year',
                                                                            $application->age
                                                                                ? \Carbon\Carbon::parse(
                                                                                    $application->age,
                                                                                )->format('Y')
                                                                                : '',
                                                                        );
                                                                    @endphp

                                                                    @for ($i = $maxYear; $i >= $minYear; $i--)
                                                                        <option value="{{ $i }}"
                                                                            {{ $selectedYear == $i ? 'selected' : '' }}>
                                                                            {{ $i }}
                                                                        </option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="employment_status">
                                                                @lang('Employment status')
                                                            </label>
                                                            <br />
                                                            <select name="employment_status" id="employment_status"
                                                                class="w-full applicant-form__select">
                                                                @foreach ($employment_statuses as $employment_status => $employment_status_name)
                                                                    <option value="{{ $employment_status }}"
                                                                        @if (old('employment_status', $application->employment_status ?? '') == $employment_status) selected @endif>
                                                                        {{ __($employment_status_name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <!-- Education -->
                                                        <div class="applicant-form__input-wrap">
                                                            <label for="education">
                                                                @lang('Education level')
                                                            </label>
                                                            <br />
                                                            <select name="education" id="education"
                                                                class="w-full applicant-form__select">
                                                                @foreach ($education_levels as $education_level => $education_level_name)
                                                                    <option value="{{ $education_level }}"
                                                                        @if (old('education', $application->education ?? '') == $education_level) selected @endif>
                                                                        {{ __($education_level_name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <!-- Netto monthly income -->
                                                        <div class="applicant-form__input-wrap">
                                                            <label for="salary">
                                                                @lang('Netto monthly income')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="number"
                                                                name="salary" id="salary" min="0"
                                                                max="99999"
                                                                value="{{ old('salary', $application->salary ?? '') }}"
                                                                placeholder="€" required />
                                                        </div>

                                                        <!-- Monthly debit -->
                                                        <div class="applicant-form__input-wrap">
                                                            <label for="salary">
                                                                @lang('Monthly debit')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="number"
                                                                name="monthly_debit" id="monthly_debit" min="0"
                                                                max="99999"
                                                                value="{{ old('monthly_debit', $application->monthly_debit ?? '') }}"
                                                                placeholder="€" required />
                                                        </div>

                                                        @if ($is_mortgage)
                                                            <!-- Additional inputs for Mortgage -->
                                                            <div class="applicant-form__input-wrap sm:block">
                                                                <label>
                                                                    <div class="flex">
                                                                        <div for="cadastral_number">
                                                                            @lang('Cadastral Number')<sup>*</sup>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <input class="applicant-form__input" type="text"
                                                                    name="cadastral_number" id="cadastral_number"
                                                                    value="{{ old('cadastral_number', $application->cadastral_number ?? '') }}"
                                                                    required />
                                                                <input class="hidden" type="number"
                                                                    name="has_real_property" id="has_real_property"
                                                                    value="1" required />
                                                            </div>
                                                        @endif

                                                        @if (!$is_mortgage)
                                                            @php
                                                                $hasRealProperty = (int) old(
                                                                    'has_real_property',
                                                                    $application->has_real_property ?? 0,
                                                                );
                                                            @endphp

                                                            <div x-data="{ value: {{ $hasRealProperty }} }"
                                                                class="applicant-form__input-wrap sm:block">
                                                                <label class="applicant-form__label">
                                                                    <div class="flex items-start">
                                                                        <div>
                                                                            @lang('Real property')<sup>*</sup>
                                                                        </div>

                                                                        <div data-tippy-content="@lang('Real property tip')"
                                                                            data-tippy-theme="loan-info" class="hasTippy">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1" stroke="currentColor"
                                                                                class="w-4 h-4 ml-1">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </label>

                                                                <input type="hidden" name="has_real_property"
                                                                    id="has_real_property" :value="value">

                                                                <div class="grid grid-cols-2 gap-2 mt-1">
                                                                    <button type="button" @click="value = 1"
                                                                        :class="value === 1 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('Yes')
                                                                    </button>

                                                                    <button type="button"
                                                                        @click="value = 0; $refs.cadastralNumber.value = ''"
                                                                        :class="value === 0 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('No')
                                                                    </button>
                                                                </div>

                                                                <div x-show="value === 1" x-transition x-cloak
                                                                    class="mt-4">
                                                                    <label class="applicant-form__label"
                                                                        for="cadastral_number">
                                                                        @lang('Cadastral Number')<sup>*</sup>
                                                                    </label>

                                                                    <input x-ref="cadastralNumber"
                                                                        class="applicant-form__input" type="text"
                                                                        name="cadastral_number" id="cadastral_number"
                                                                        x-bind:required="value === 1"
                                                                        x-bind:disabled="value !== 1"
                                                                        value="{{ old('cadastral_number', $application->cadastral_number ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($is_car_mortgage)
                                                            <!-- Additional inputs for Car Mortgage -->
                                                            <div class="applicant-form__input-wrap sm:block">
                                                                <label>
                                                                    <div class="flex">
                                                                        <div for="car_number">
                                                                            @lang('Car Number')<sup>*</sup>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <input class="applicant-form__input" type="text"
                                                                    name="car_number" id="car_number"
                                                                    value="{{ old('car_number', $application->car_number ?? '') }}"
                                                                    required />
                                                                <input class="hidden" type="number" name="has_car"
                                                                    id="has_car" value="1" required />
                                                            </div>
                                                        @endif

                                                        @if (!$is_car_mortgage)
                                                            @php
                                                                $hasCar = (int) old(
                                                                    'has_car',
                                                                    $application->has_car ?? 0,
                                                                );
                                                            @endphp

                                                            <div x-data="{ value: {{ $hasCar }} }"
                                                                class="applicant-form__input-wrap sm:block">
                                                                <label class="applicant-form__label">
                                                                    <div class="flex">
                                                                        <div for="has_car">
                                                                            @lang('Has car')<sup>*</sup>
                                                                        </div>
                                                                    </div>
                                                                </label>

                                                                <input type="hidden" name="has_car" id="has_car"
                                                                    :value="value">

                                                                <div class="grid grid-cols-2 gap-2 mt-1">
                                                                    <button type="button" @click="value = 1"
                                                                        :class="value === 1 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('Yes')
                                                                    </button>

                                                                    <button type="button"
                                                                        @click="value = 0; $refs.carNumber.value = ''"
                                                                        :class="value === 0 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('No')
                                                                    </button>
                                                                </div>

                                                                <div x-show="value === 1" x-transition x-cloak
                                                                    class="mt-4">
                                                                    <label class="applicant-form__label" for="car_number">
                                                                        @lang('Car Number')<sup>*</sup>
                                                                    </label>

                                                                    <input x-ref="carNumber" class="applicant-form__input"
                                                                        type="text" name="car_number" id="car_number"
                                                                        x-bind:required="value === 1"
                                                                        x-bind:disabled="value !== 1"
                                                                        value="{{ old('car_number', $application->car_number ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="address_street">
                                                                @lang('Address')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="address_street" id="address_street" placeholder=""
                                                                maxlength="63"
                                                                value="{{ old('address_street', $application->address_street ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="flex flex-row gap-x-2">
                                                            <div class="applicant-form__input-wrap" style="flex: 3;">
                                                                <label for="address_city">
                                                                    @lang('City')<sup>*</sup>
                                                                </label>
                                                                <input class="applicant-form__input" type="text"
                                                                    name="address_city" id="address_city" maxlength="31"
                                                                    value="{{ old('address_city', $application->address_city ?? '') }}"
                                                                    required />
                                                            </div>

                                                            <div class="applicant-form__input-wrap" style="flex: 3;">
                                                                <label for="address_zipcode">
                                                                    @lang('Zipcode')(LV-****)<sup>*</sup>
                                                                </label>
                                                                <input
                                                                    class="applicant-form__input applyZipcodeMask placeholder:text-gray-600"
                                                                    type="text" name="address_zipcode"
                                                                    placeholder="LV-" id="address_zipcode" maxlength="7"
                                                                    value="{{ old('address_zipcode', $application->address_zipcode ?? '') }}" />
                                                            </div>
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="marital_status">
                                                                @lang('Marital status')
                                                            </label>
                                                            <br />
                                                            <select name="marital_status" id="marital_status"
                                                                class="w-full applicant-form__select">
                                                                @foreach ($marital_statuses as $marital_status => $marital_statuses_name)
                                                                    <option value="{{ $marital_status }}"
                                                                        @if (old('marital_status', $application->marital_status ?? '') == $marital_status) selected @endif>
                                                                        {{ __($marital_statuses_name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="dependants">
                                                                @lang('Dependants')<sup>*</sup>
                                                            </label>
                                                            <input class="applicant-form__input" type="number"
                                                                min="0" max="99" step="1"
                                                                name="dependants" id="dependants"
                                                                value="{{ old('dependants', $application->dependants ?? '') }}"
                                                                required />
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <div class="flex flex-col items-start justify-start">
                                                                <label for="is_political_person">
                                                                    <div class="flex items-start">
                                                                        <div for="is_political_person">
                                                                            @lang('applicant_form.is_political_person')
                                                                        </div>
                                                                        <div data-tippy-content="@lang('applicant_form.is_political_person_desc')"
                                                                            data-tippy-theme="loan-info" class="hasTippy">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1" stroke="currentColor"
                                                                                class="w-4 h-4 ml-1">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v .008H12v-.008Z" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </label>

                                                                @php
                                                                    $isPoliticalPerson = (int) old(
                                                                        'is_political_person',
                                                                        $application->is_political_person ?? 0,
                                                                    );
                                                                @endphp
                                                                <div x-data="{ value: {{ $isPoliticalPerson }} }"
                                                                    class="grid w-full grid-cols-2 gap-2 mt-0.5">
                                                                    <input type="hidden" name="is_political_person"
                                                                        id="is_political_person" :value="value">

                                                                    <button type="button" @click="value = 1"
                                                                        :class="value === 1 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('Yes')
                                                                    </button>

                                                                    <button type="button" @click="value = 0"
                                                                        :class="value === 0 ?
                                                                            'border-primary-normal bg-primary-normal text-white' :
                                                                            'border-gray-300 bg-white text-gray-700'"
                                                                        class="w-full px-3 py-3.5 text-sm font-medium transition border rounded-md">
                                                                        @lang('No')
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="applicant-form__input-wrap">
                                                            <label for="disbursement_iban">
                                                                @lang('IBAN to receive money to')
                                                            </label>
                                                            <input class="applicant-form__input" type="text"
                                                                name="disbursement_iban" id="disbursement_iban"
                                                                maxlength="45"
                                                                value="{{ old('disbursement_iban', $application->disbursement_iban ?? '') }}" />
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div
                                                class="hidden md:grid grid-cols-1 md:grid-cols-[1fr_3fr] gap-2 md:gap-4 items-center mt-4">
                                                @if ($step < Application::MAX_STEPS && !empty($go_back_link))
                                                    <!-- Go back button -->
                                                    <a href="{{ $go_back_link }}"
                                                        class="order-2 block px-5 md:order-1 applicant-form__back-button"
                                                        id="back_button" type="button">
                                                        <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                            <svg class="h-5 mt-1 mr-2" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 19l-7-7 7-7" />
                                                            </svg>
                                                            @lang('main.back')
                                                            <div></div>
                                                        </div>
                                                    </a>

                                                    <!-- Submit button -->
                                                    <button
                                                        class="flex flex-row items-center justify-center order-1 text-lg applicant-form__button md:order-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        id="submit_button" type="submit">
                                                        <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                            <div></div>
                                                            @if (!empty($application->phone_verified_at))
                                                                @lang('Continue')
                                                            @else
                                                                @lang('main.confirm')
                                                            @endif

                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="h-5 mt-1 ml-2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                            </svg>
                                                        </div>

                                                        @include('includes.loading-spinner')
                                                    </button>
                                                @else
                                                    <!-- Submit button -->
                                                    <button
                                                        class="flex flex-row items-center justify-center text-lg applicant-form__button disabled:opacity-50 disabled:cursor-not-allowed"
                                                        id="submit_button" type="submit">

                                                        @if (!empty($application->phone_verified_at))
                                                            @lang('Continue')
                                                        @else
                                                            @lang('main.confirm')
                                                        @endif

                                                        @include('includes.loading-spinner')
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__aside">
                    @include('includes.applicant-form._aside')
                </div>
                <div class="hidden lg:block"></div>
            </div>
        </section>
        <!-- Sticky footer nav buttons -->
        @include('includes.applicant-form._sticky_footer')
    </main>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const applicantForm = document.getElementById('applicant_form');
            if (applicantForm && applicantForm.getElementsByClassName('hasTippy').length > 0) {
                tippy('[data-tippy-content]', {
                    theme: 'loan-info'
                });
            }
        });
    </script>
@endsection
