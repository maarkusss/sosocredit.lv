@php
    use App\Models\Application;
    use App\Models\Lender;
    use App\ValueObjects\LenderConfig;

    /** @var Application $application
    * @var LenderConfig $provider
    * @var Lender $lender
    * @var string $source
    * @var array $required_fields
    * @var array $offer_details
    * */

    $locale = app()->getLocale();
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <section class="py-5 section_narrower md:section sm:py-8">
            <div class="lg:grid ">
                <div class="hidden lg:block"></div>
                <div class="section__content">
                    <div class="flex flex-col items-center justify-center">
                        <div class="landing-form-wrapper applicant-form">
                            <div class="w-full">
                                <div class="landing-form__steps">
                                    <form class="grid grid-cols-1 gap-4"
                                          id="applicant_form"
                                          method="POST"
                                          action="{{ route('consumers.applications.open-link', [$application->token, $provider->name, $source]) }}">
                                        @csrf

                                        <div class="applicant-form__wrapper">
                                            <div>
                                                <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
                                                    <div class="applicant-form__input-wrap">
                                                        <p class="text-lg md:text-2xl font-bold text-left pb-2 mb-2">
                                                            @lang('ADF header')
                                                        </p>
                                                        @foreach($required_fields as $name => $attributes)
                                                            @continue( ! $attributes['enabled'])

                                                            @php
                                                                $label = $attributes['translations']['label'][$locale] ?? null;
                                                                $placeholder = $attributes['translations']['placeholder'][$locale] ?? '';
                                                                $required = (bool) $attributes['required'];
                                                            @endphp

                                                            @switch($attributes['type'])
                                                                @case('select')
{{--                                                                    @if($label)--}}
{{--                                                                        <label for="{{ $name }}">--}}
{{--                                                                            {{ $label }} @if($required)<sup>*</sup>@endif--}}
{{--                                                                        </label>--}}
{{--                                                                    @endif--}}

                                                                    <select class="applicant-form__input border border-gray-300 mb-2"
                                                                            name="{{ $name }}" id="{{ $name }}"
                                                                            @if($required) required @endif>
                                                                        <option value="" disabled @selected(old($name) === null)>{{ $placeholder }}@if($required)<sup>*</sup>@endif</option>
                                                                        @foreach($attributes['options'] ?? [] as $value => $translations)
                                                                            <option value="{{ $value }}" @selected(old($name) === (string) $value)>
                                                                                {{ $translations[$locale] ?? $value }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @break

                                                                @case('checkbox')
                                                                    <label class="inline-flex items-center gap-2 mb-2" for="{{ $name }}">
                                                                        <input type="hidden" name="{{ $name }}" value="0" />
                                                                        <input type="checkbox"
                                                                               name="{{ $name }}" id="{{ $name }}"
                                                                               value="1"
                                                                               @checked(old($name) === '1')
                                                                               @if($required) required @endif />
                                                                        {{ $label }} @if($required)<sup>*</sup>@endif
                                                                    </label>
                                                                    @break

                                                                @default
{{--                                                                    @if($label)--}}
{{--                                                                        <label for="{{ $name }}">--}}
{{--                                                                            {{ $label }} @if($required)<sup>*</sup>@endif--}}
{{--                                                                        </label>--}}
{{--                                                                    @endif--}}
{{--                                                                    <input class="applicant-form__input border border-gray-300 mb-2"--}}
{{--                                                                           type="{{ $attributes['type'] }}"--}}
{{--                                                                           name="{{ $name }}" id="{{ $name }}"--}}
{{--                                                                           value="{{ old($name, '') }}"--}}
{{--                                                                           placeholder="{{ $placeholder }}@if($required)*@endif"--}}
{{--                                                                           @if($attributes['type'] === 'text' && ! empty($attributes['length'])) maxlength="{{ $attributes['length'] }}" @endif--}}
{{--                                                                           @if($required) required @endif />--}}

{{--                                                                    includes validation--}}
                                                                    <input class="applicant-form__input border border-gray-300 mb-2"
                                                                           type="{{ $attributes['type'] }}"
                                                                           name="{{ $name }}" id="{{ $name }}"
                                                                           value="{{ old($name, '') }}"
                                                                           placeholder="{{ $placeholder }}@if($required)*@endif"
                                                                           @if($attributes['type'] === 'text' && ! empty($attributes['length'])) maxlength="{{ $attributes['length'] }}" @endif
                                                                           @if(! empty($attributes['validation']))
                                                                               pattern="{{ $attributes['validation'] }}"
                                                                           oninvalid="this.setCustomValidity(this.validity.patternMismatch ? {{ json_encode(__('Invalid document number format')) }} : '')"
                                                                           oninput="this.setCustomValidity('')"
                                                                           @endif
                                                                           @if($required) required @endif />
                                                                    @error($name)
                                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                                    @enderror
                                                            @endswitch
                                                        @endforeach
{{--                                                        modal with additional info--}}
                                                        <div
                                                            class="relative pt-2"
                                                            x-data="{ open: false }"
                                                            x-on:click.outside="open = false"
                                                            x-on:keydown.escape.window="open = false"
                                                        >
                                                            <!-- Trigger -->
                                                            <div class="inline-flex items-center cursor-pointer" x-on:click.prevent="open = !open">
                                                                <svg class="block" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M10.9375 14.0625C10.9375 14.2479 10.8825 14.4292 10.7795 14.5833C10.6765 14.7375 10.5301 14.8577 10.3588 14.9286C10.1875 14.9996 9.99896 15.0182 9.81711 14.982C9.63525 14.9458 9.4682 14.8565 9.33709 14.7254C9.20598 14.5943 9.11669 14.4273 9.08052 14.2454C9.04434 14.0635 9.06291 13.875 9.13387 13.7037C9.20482 13.5324 9.32499 13.386 9.47916 13.283C9.63333 13.18 9.81458 13.125 10 13.125C10.2486 13.125 10.4871 13.2238 10.6629 13.3996C10.8387 13.5754 10.9375 13.8139 10.9375 14.0625ZM10 5.625C8.27657 5.625 6.875 6.88672 6.875 8.4375V8.75C6.875 8.91576 6.94085 9.07473 7.05806 9.19194C7.17527 9.30915 7.33424 9.375 7.5 9.375C7.66576 9.375 7.82474 9.30915 7.94195 9.19194C8.05916 9.07473 8.125 8.91576 8.125 8.75V8.4375C8.125 7.57812 8.96641 6.875 10 6.875C11.0336 6.875 11.875 7.57812 11.875 8.4375C11.875 9.29688 11.0336 10 10 10C9.83424 10 9.67527 10.0658 9.55806 10.1831C9.44085 10.3003 9.375 10.4592 9.375 10.625V11.25C9.375 11.4158 9.44085 11.5747 9.55806 11.6919C9.67527 11.8092 9.83424 11.875 10 11.875C10.1658 11.875 10.3247 11.8092 10.4419 11.6919C10.5592 11.5747 10.625 11.4158 10.625 11.25V11.1938C12.05 10.932 13.125 9.79531 13.125 8.4375C13.125 6.88672 11.7234 5.625 10 5.625ZM18.125 10C18.125 11.607 17.6485 13.1779 16.7557 14.514C15.8629 15.8502 14.594 16.8916 13.1093 17.5065C11.6247 18.1215 9.99099 18.2824 8.4149 17.9689C6.8388 17.6554 5.39106 16.8815 4.25476 15.7452C3.11846 14.6089 2.34463 13.1612 2.03112 11.5851C1.71762 10.009 1.87852 8.37535 2.49348 6.8907C3.10844 5.40605 4.14985 4.1371 5.486 3.24431C6.82214 2.35152 8.39303 1.875 10 1.875C12.1542 1.87727 14.2195 2.73403 15.7427 4.25727C17.266 5.78051 18.1227 7.84581 18.125 10ZM16.875 10C16.875 8.64025 16.4718 7.31104 15.7164 6.18045C14.9609 5.04987 13.8872 4.16868 12.631 3.64833C11.3747 3.12798 9.99238 2.99183 8.65876 3.2571C7.32514 3.52237 6.10013 4.17716 5.13864 5.13864C4.17716 6.10013 3.52238 7.32513 3.2571 8.65875C2.99183 9.99237 3.12798 11.3747 3.64833 12.6309C4.16868 13.8872 5.04987 14.9609 6.18046 15.7164C7.31105 16.4718 8.64026 16.875 10 16.875C11.8227 16.8729 13.5702 16.1479 14.8591 14.8591C16.1479 13.5702 16.8729 11.8227 16.875 10Z" fill="#297935"/>
                                                                    </svg>
                                                                </svg>
                                                                <p class="text-xs leading-tight text-green-800 ml-1">
                                                                    @lang('ADF reason')
                                                                </p>
                                                            </div>

                                                            <!-- Mobile backdrop -->
                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                class="fixed inset-0 z-[1049] bg-black/40 md:hidden"
                                                                x-on:click="open = false"
                                                            ></div>

                                                            <!-- Popup: centered modal on mobile, popover above the icon on desktop -->
                                                            <div
                                                                x-show="open"
                                                                x-cloak
                                                                class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-[1050]
                                                                   w-[calc(100vw-2rem)] max-w-md max-h-[90vh] overflow-y-auto
                                                                   rounded-lg border border-gray-200 bg-white p-6 shadow-lg
                                                                   md:absolute md:top-auto md:bottom-full md:left-[10px] md:mb-3
                                                                   md:translate-y-0 md:w-72 md:max-h-none md:overflow-visible md:p-4"
                                                                >
                                                                <p class="font-bold mb-2">@lang('ADF description header')</p>
                                                                <p class="text-sm">@lang('ADF description')</p>

                                                                <!-- Arrow pointing at the icon (desktop only) -->
                                                                <div class="hidden md:block absolute left-1/2 -bottom-[7px] -translate-x-1/2 w-3 h-3 rotate-45 bg-white border-r border-b border-gray-200"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @include('includes.applicant-form.partials.adf_offer_details', ['class' => 'hidden md:block'])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="items-center justify-center justify-items-center">
                                            <button
                                                class="flex flex-row items-center justify-between text-lg applicant-form__button disabled:opacity-50 disabled:cursor-not-allowed"
                                                id="submit_button" type="submit">
                                                <div> </div>
                                                @lang('Take offer')
                                                @include('includes.loading-spinner')
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.45234 6.27654C8.62963 6.09946 8.86996 6 9.12054 6C9.37112 6 9.61145 6.09946 9.78874 6.27654L15.5 11.9878L9.78874 17.699C9.70218 17.7919 9.59779 17.8665 9.4818 17.9181C9.36581 17.9698 9.2406 17.9976 9.11363 17.9999C8.98667 18.0021 8.86056 17.9787 8.74282 17.9312C8.62508 17.8836 8.51812 17.8128 8.42833 17.723C8.33854 17.6333 8.26776 17.5263 8.2202 17.4086C8.17264 17.2908 8.14929 17.1647 8.15153 17.0377C8.15377 16.9108 8.18156 16.7856 8.23324 16.6696C8.28492 16.5536 8.35943 16.4492 8.45234 16.3626L12.8272 11.9878L8.45234 7.61294C8.27526 7.43565 8.1758 7.19532 8.1758 6.94474C8.1758 6.69416 8.27526 6.45383 8.45234 6.27654Z" fill-rule="evenodd" clip-rule="evenodd" fill="white"/>
                                                </svg>
                                            </button>
                                        </div>
                                        @include('includes.applicant-form.partials.adf_offer_details', ['class' => 'md:hidden'])
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block"></div>
            </div>
        </section>
    </main>
@endsection
