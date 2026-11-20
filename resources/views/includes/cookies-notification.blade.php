<div x-data="cookieModal" x-show="open" x-cloak
    @open-cookie-modal.window="open = true; tab = $event.detail?.tab ?? 'consent'; mobileDetails = (tab === 'details')"
    class="fixed inset-x-0 z-50 bg-white shadow-[0_12px_32px_0_rgba(0,0,0,0.15)] flex items-center justify-center bottom-0">
    <div class="w-full max-w-[1170px] lg:px-0 px-4 py-6 mx-auto">
        <div class="hidden gap-4 pb-3 mb-2 sm:flex">
            <button @click="tab = 'consent'"
                :class="tab === 'consent' ? 'font-medium border-primary-normal text-primary-normal' : 'text-gray-400'"
                class="text-sm border-b">
                @lang('cookies.cookies_consent')
            </button>

            <button @click="tab = 'details'"
                :class="tab === 'details' ? 'font-medium border-primary-normal text-primary-normal' : 'text-gray-400'"
                class="text-sm border-b">
                @lang('cookies.cookies_details')
            </button>

            <button @click="tab = 'about'"
                :class="tab === 'about' ? 'font-medium border-primary-normal text-primary-normal' : 'text-gray-400'"
                class="text-sm border-b">
                @lang('cookies.cookies_about')
            </button>
        </div>

        <div x-show="tab === 'consent'" x-cloak>
            <div class="flex items-start justify-between gap-4">
                <div class="text-sm font-semibold text-gray-900">
                    @lang('cookies.cookies_consent_title')
                </div>
                <button @click="rejectAll" type="button" class="block sm:hidden"
                    aria-label="{{ __('cookies.cookies_reject') }}">
                    <svg class="w-5 h-5 text-gray-800 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                        focusable="false">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="mt-2 text-sm text-gray-600">
                @lang('cookies.cookies_consent_description')
            </p>

            <div
                class="flex-col hidden w-full gap-4 mt-4 rounded-lg sm:flex sm:p-3 sm:flex-row sm:flex-wrap sm:items-center sm:border">
                <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <div class="flex items-center justify-between w-full gap-2 md:w-auto">
                        <div class="flex items-center text-sm font-medium text-gray-700 gap-x-1">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1329_7822)">
                                    <path
                                        d="M14.0625 6.75H13.5V4.5C13.5 2.01825 11.4818 0 9 0C6.51825 0 4.5 2.01825 4.5 4.5V6.75H3.9375C3.0075 6.75 2.25 7.50675 2.25 8.4375V16.3125C2.25 17.2432 3.0075 18 3.9375 18H14.0625C14.9925 18 15.75 17.2432 15.75 16.3125V8.4375C15.75 7.50675 14.9925 6.75 14.0625 6.75ZM6 4.5C6 2.8455 7.3455 1.5 9 1.5C10.6545 1.5 12 2.8455 12 4.5V6.75H6V4.5ZM9.75 12.5415V14.25C9.75 14.664 9.41475 15 9 15C8.58525 15 8.25 14.664 8.25 14.25V12.5415C7.80375 12.2812 7.5 11.8027 7.5 11.25C7.5 10.4227 8.17275 9.75 9 9.75C9.82725 9.75 10.5 10.4227 10.5 11.25C10.5 11.8027 10.1962 12.2812 9.75 12.5415Z"
                                        fill="#34314C" fill-opacity="0.6" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_1329_7822">
                                        <rect width="18" height="18" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            @lang('cookies.cookies_necessary')
                        </div>
                        <span class="relative inline-flex items-center">
                            <span class="h-5 rounded-full bg-primary-normal w-9"></span>
                            <span
                                class="absolute right-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full"></span>
                        </span>
                    </div>
                </div>

                <!-- ANALYTICS -->
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <div class="flex items-center justify-between w-full gap-2 sm:w-auto">
                        <span class="text-sm font-medium text-gray-700">
                            @lang('cookies.cookies_analytics')
                        </span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="consent.analytics" class="sr-only peer">
                            <span
                                class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                            <span
                                class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                        </div>
                    </div>
                </label>

                <!-- MARKETING -->
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <div class="flex items-center justify-between w-full gap-2 sm:w-auto">
                        <span class="text-sm font-medium text-gray-700">
                            @lang('cookies.cookies_marketing')
                        </span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="consent.marketing" class="sr-only peer">
                            <span
                                class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                            <span
                                class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                        </div>
                    </div>
                </label>

                <!-- FUNCTIONAL -->
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <div class="flex items-center justify-between w-full gap-2 sm:w-auto">
                        <span class="text-sm font-medium text-gray-700">
                            @lang('cookies.cookies_functional')
                        </span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="consent.functional" class="sr-only peer">
                            <span
                                class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                            <span
                                class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- DETAILS --}}
        <div x-show="tab === 'details'" x-cloak class="space-y-3">
            <!-- Necessary -->
            <div class="p-3 border rounded-lg">
                <button @click="toggle('necessary')"
                    class="flex items-center justify-between w-full text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <svg :class="openSection === 'necessary' ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>@lang('cookies.cookies_necessary')</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-not-allowed opacity-70">
                        <input type="checkbox" checked disabled class="sr-only peer">
                        <span class="h-5 rounded-full w-9 bg-primary-normal"></span>
                        <span class="absolute right-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full"></span>
                    </label>
                </button>
                <div x-show="openSection === 'necessary'" x-cloak class="mt-3 text-sm text-gray-500">
                    @lang('cookies.cookies_necessary_description')
                </div>
            </div>

            <!-- Analytics -->
            <div class="p-3 border rounded-lg">
                <button @click="toggle('analytics')"
                    class="flex items-center justify-between w-full text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <svg :class="openSection === 'analytics' ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>@lang('cookies.cookies_analytics')</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="consent.analytics" class="sr-only peer">
                        <span
                            class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                        <span
                            class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                    </label>
                </button>
                <div x-show="openSection === 'analytics'" x-cloak class="mt-3 text-sm text-gray-500">
                    @lang('cookies.cookies_analytics_description')
                </div>
            </div>

            <!-- Marketing -->
            <div class="p-3 border rounded-lg">
                <button @click="toggle('marketing')"
                    class="flex items-center justify-between w-full text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <svg :class="openSection === 'marketing' ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>@lang('cookies.cookies_marketing')</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="consent.marketing" class="sr-only peer">
                        <span
                            class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                        <span
                            class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                    </label>
                </button>
                <div x-show="openSection === 'marketing'" x-cloak class="mt-3 text-sm text-gray-500">
                    @lang('cookies.cookies_marketing_description')
                </div>
            </div>

            <!-- Functional -->
            <div class="p-3 border rounded-lg">
                <button @click="toggle('functional')"
                    class="flex items-center justify-between w-full text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <svg :class="openSection === 'functional' ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>@lang('cookies.cookies_functional')</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="consent.functional" class="sr-only peer">
                        <span
                            class="h-5 transition bg-gray-200 rounded-full w-9 peer-checked:bg-primary-normal"></span>
                        <span
                            class="absolute left-0.5 top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></span>
                    </label>
                </button>
                <div x-show="openSection === 'functional'" x-cloak class="mt-3 text-sm text-gray-500">
                    @lang('cookies.cookies_functional_description')
                </div>
            </div>
        </div>

        {{-- ABOUT --}}
        <div x-show="tab === 'about'" x-cloak class="text-sm text-gray-600">
            @lang('cookies.cookies_about_description')
        </div>

        {{-- BUTTONS --}}
        <div
            class="grid grid-cols-1 gap-3 mt-6 max-w-[640px] ml-auto place-content-center items-center sm:grid-cols-3">
            <button @click="acceptAll"
                class="py-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-primary-normal hover:bg-primary-dark">
                @lang('cookies.cookies_accept_all')
            </button>
            <button @click="acceptSelected"
                :class="{
                    'hidden': !mobileDetails,
                    'block': mobileDetails
                }"
                class="py-2 text-sm font-semibold transition-all duration-200 border rounded-lg text-primary-normal sm:block border-primary-normal hover:bg-primary-normal/10">
                @lang('cookies.cookies_accept_selected')
            </button>
            <button @click="rejectAll"
                class="py-2 text-sm font-semibold transition-all duration-200 border rounded-lg text-primary-normal border-primary-normal hover:bg-primary-normal/10">
                @lang('cookies.cookies_reject')
            </button>

            <button
                @click="mobileDetails ? (mobileDetails = false, tab = 'consent') : (mobileDetails = true, tab = 'details')"
                class="flex items-center justify-center text-sm gap-x-2 sm:hidden">
                <template x-if="!mobileDetails">
                    <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </template>

                <template x-if="mobileDetails">
                    <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                </template>
                <span x-text="mobileDetails ? '@lang('cookies.cookies_back')' : '@lang('cookies.cookies_adjust')'"></span>
            </button>
        </div>

    </div>
</div>
