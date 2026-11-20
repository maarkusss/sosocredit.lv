@php

    $user = Auth::guard('consumers')->user();
    $whatsapp_url = config('app.whatsapp.msgs_url');
    $whatsapp_url =
        App::getLocale() == 'ru'
            ? $whatsapp_url . config('app.whatsapp.ru')
            : $whatsapp_url . config('app.whatsapp.lv');

@endphp

<header class="header" id="siteHeader">
    <div class="header__wrapper">
        <div class="flex flex-row items-center justify-start">
            <x-application-logo is_applicant_page="{{ !empty($is_applicant_page) ? 1 : 0 }}"></x-application-logo>

            @if (!empty($header_menu))
                <div class="header__menu" id="headerNavbar">
                    <ul>
                        @if (!empty($header_menu))
                            @foreach ($header_menu as $url => $title)
                                <li class="px-3">
                                    <a class="@if (!empty(parse_url(request()->getRequestUri())['path']) && $url == parse_url(request()->getRequestUri())['path']) active @endif" href="{{ $url }}"
                                        title="{{ $title }}">
                                        <span>{{ $title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            @endif
        </div>
        <div class="flex flex-row justify-start">

            @if (!empty($show_broker_phone_in_header))
                <div class="hidden md:block place-content-center">
                    <div class="flex flex-row items-center justify-center text-base leading-6 sm:mr-8 sm:leading-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M16.5057 20.0011C15.2796 19.9559 11.8049 19.4757 8.16587 15.8375C4.52767 12.1985 4.04831 8.7246 4.00233 7.49768C3.93422 5.62792 5.36634 3.8118 7.02069 3.10255C7.2199 3.01653 7.43806 2.98378 7.65376 3.00751C7.86945 3.03124 8.07527 3.11065 8.25102 3.23793C9.61332 4.23071 10.5533 5.73265 11.3605 6.91359C11.5381 7.17305 11.614 7.48878 11.5738 7.80062C11.5336 8.11246 11.3801 8.39861 11.1425 8.60455L9.48135 9.83829C9.40109 9.89624 9.3446 9.98135 9.32236 10.0778C9.30013 10.1743 9.31366 10.2755 9.36044 10.3628C9.73678 11.0465 10.406 12.0648 11.1723 12.8311C11.9386 13.5974 13.0055 14.3109 13.7368 14.7298C13.8285 14.7813 13.9365 14.7957 14.0385 14.77C14.1405 14.7444 14.2287 14.6806 14.2852 14.5919L15.3665 12.946C15.5653 12.682 15.8586 12.5051 16.1849 12.4524C16.5112 12.3997 16.8452 12.4753 17.1171 12.6634C18.315 13.4927 19.7131 14.4165 20.7365 15.7268C20.8741 15.9039 20.9617 16.1146 20.99 16.337C21.0183 16.5594 20.9863 16.7854 20.8974 16.9912C20.1848 18.6541 18.3814 20.07 16.5057 20.0011Z"
                                fill="#081B1A" />
                        </svg>
                        <p class="ml-1 text-lg font-bold leading-normal text-neutral-900">
                            <a href="tel:{{ str_replace(' ', '', config('app.broker_phone')) }}">
                                {{ config('app.broker_phone') }}
                            </a>
                        </p>
                    </div>
                </div>
            @endif

            <div class="block md:hidden place-content-center">
                <a href="{{ $whatsapp_url }}" aria-label="whatsapp">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_919_1534)">
                            <path
                                d="M27.2451 4.64697C24.239 1.6521 20.24 0.00170899 15.9839 0C11.7373 0 7.73193 1.64893 4.70605 4.64307C1.6748 7.64233 0.00390625 11.6282 0 15.8518V15.8567V15.8596C0.000488281 18.416 0.672119 20.9949 1.94702 23.3489L0.0437012 32L8.79443 30.0095C11.0107 31.1265 13.4836 31.7153 15.9778 31.7163H15.9841C20.23 31.7163 24.2354 30.0671 27.2617 27.0728C30.2957 24.071 31.9673 20.0903 31.9692 15.864C31.9705 11.6675 30.293 7.68384 27.2451 4.64697ZM15.9839 29.2188H15.9783C13.7388 29.2178 11.5198 28.6555 9.56152 27.5923L9.14771 27.3677L3.32886 28.6912L4.59277 22.947L4.34912 22.5269C3.1377 20.4377 2.49756 18.1316 2.49756 15.8569C2.5022 8.49438 8.55151 2.49756 15.9834 2.49756C19.5737 2.49902 22.9473 3.89087 25.4824 6.41626C28.0559 8.98071 29.4727 12.3357 29.4714 15.8633C29.4685 23.2275 23.418 29.2188 15.9839 29.2188Z"
                                fill="#3FAF4F" />
                            <path
                                d="M11.635 8.86646H10.9343C10.6904 8.86646 10.2944 8.95776 9.95947 9.32227C9.62427 9.68701 8.67969 10.5686 8.67969 12.3616C8.67969 14.1545 9.98999 15.887 10.1726 16.1304C10.3555 16.3735 12.7019 20.1704 16.4182 21.6311C19.5068 22.845 20.1355 22.6035 20.8057 22.5427C21.4761 22.4822 22.969 21.6614 23.2737 20.8105C23.5784 19.9597 23.5784 19.2302 23.4871 19.0779C23.3955 18.926 23.1516 18.835 22.7861 18.6528C22.4204 18.4705 20.6284 17.574 20.2932 17.4521C19.958 17.3308 19.7144 17.27 19.4705 17.635C19.2266 17.9993 18.5085 18.842 18.2952 19.0852C18.082 19.3286 17.8687 19.3591 17.5029 19.1768C17.1372 18.9939 15.9717 18.6023 14.575 17.3611C13.4878 16.395 12.7334 15.1631 12.52 14.7983C12.3069 14.4338 12.4973 14.2366 12.6807 14.0547C12.845 13.8916 13.0669 13.6685 13.2498 13.4558C13.4324 13.2429 13.4844 13.0911 13.6064 12.8479C13.7283 12.6047 13.6672 12.3918 13.5759 12.2097C13.4844 12.0273 12.7827 10.2253 12.4575 9.50464H12.4578C12.1838 8.89771 11.8955 8.8772 11.635 8.86646Z"
                                fill="#3FAF4F" />
                        </g>
                        <defs>
                            <clipPath id="clip0_919_1534">
                                <rect width="32" height="32" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </a>
            </div>

            @if (!empty($locales_switcher))
                <div class="header__wrapper__locales">
                    <div class="relative">
                        <button
                            class="px-1 py-2 mx-4 header__wrapper__locales__button sm:px-2 text_sm font-sm link_primary"
                            id="navbarDropdown" type="button">

                            {{ strtoupper(app()->getLocale()) }}

                            <svg class="w-3 h-3 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{--                        Mobile view for local switcher --}}
                        {{--                        <ul class=" --}}
                        {{--                            hidden --}}
                        {{--                            fixed md:absolute top-[24px] md:top-2 left-0 right-0 md:right-5 md:z-10 --}}
                        {{--                            -mb-2 --}}
                        {{--                            w-full md:w-16 --}}
                        {{--                            bg-white --}}
                        {{--                            border-t md:border --}}
                        {{--                            items-start --}}
                        {{--                            space-y-4 --}}
                        {{--                            border-neutral-300 --}}
                        {{--                            text-neutral-900 --}}
                        {{--                            md:rounded-md --}}
                        {{--                            shadow-lg --}}
                        {{--                            transform translate-y-full" --}}
                        {{--                            id="navbarDropdownList"> --}}

                        <ul class="absolute left-0 z-10 items-start hidden w-16 -mb-2 space-y-4 transform translate-y-full bg-white border rounded-md shadow-lg top-2 right-5 border-neutral-300 text-neutral-900"
                            id="navbarDropdownList">
                            @foreach ($locales_switcher as $locale_switch)
                                <li class="header__wrapper__locales__dropdown__li">
                                    <a class="header__wrapper__locales__dropdown__a text_neutral-900 link_primary"
                                        href="{{ $locale_switch['href'] }}">
                                        {{ $locale_switch['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @elseif (!empty(inactive_locales()))
                <div class="header__wrapper__locales">
                    <div class="relative">
                        <button class="header__wrapper__locales__button" id="navbarDropdown" type="button">

                            {{ strtoupper(app()->getLocale()) }}

                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        @if (count(config('app.available_locales')) > 1)
                            <ul class="header__wrapper__locales__dropdown
                                    fixed md:absolute
                                    top-[62px] md:top-12
                                    right-0
                                    z-10
                                    hidden
                                    -mb-2
                                    transform
                                    translate-y-full"
                                id="navbarDropdownList">
                                @foreach (inactive_locales() as $inactive_locale)
                                    <li class="header__wrapper__locales__dropdown__li">
                                        <a class="header__wrapper__locales__dropdown__a"
                                            href="/{{ $inactive_locale }}">
                                            {{ strtoupper($inactive_locale) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            @if (config('app.broker_enabled'))
                @if (!empty($user->id))
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            class="flex flex-row items-center justify-start gap-1 px-3 py-2 text-sm md:text-base font-semibold leading-tight text-center text-white border border-green-600 rounded bg-[#3FAF4F] hover:bg-green-700 md:px-4 cursor-pointer transition-colors shadow-sm"
                            @click="open = !open"
                            type="button">

                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10 10.625C7.35837 10.625 5.20837 8.47502 5.20837 5.83335C5.20837 3.19169 7.35837 1.04169 10 1.04169C12.6417 1.04169 14.7917 3.19169 14.7917 5.83335C14.7917 8.47502 12.6417 10.625 10 10.625ZM10 2.29169C8.05004 2.29169 6.45837 3.88335 6.45837 5.83335C6.45837 7.78335 8.05004 9.37502 10 9.37502C11.95 9.37502 13.5417 7.78335 13.5417 5.83335C13.5417 3.88335 11.95 2.29169 10 2.29169Z"
                                        fill="white" />
                                    <path
                                        d="M17.1584 18.9583C16.8167 18.9583 16.5334 18.675 16.5334 18.3333C16.5334 15.4583 13.6002 13.125 10.0002 13.125C6.40012 13.125 3.4668 15.4583 3.4668 18.3333C3.4668 18.675 3.18346 18.9583 2.8418 18.9583C2.50013 18.9583 2.2168 18.675 2.2168 18.3333C2.2168 14.775 5.70846 11.875 10.0002 11.875C14.2918 11.875 17.7834 14.775 17.7834 18.3333C17.7834 18.675 17.5001 18.9583 17.1584 18.9583Z"
                                        fill="white" />
                                </svg>

                                <span class="hidden md:block">
                                    @if (!empty($user->first_name))
                                        {{ $user->first_name }}
                                    @else
                                        {{ __('Profile') }}
                                    @endif
                                </span>

                                <svg class="w-3.5 h-3.5 ml-1 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 20 20" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                class="absolute right-0 top-full mt-2 z-50 p-3 w-52 bg-white border border-neutral-200 text-neutral-900 rounded-lg shadow-xl"
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                x-cloak>
                                <ul class="space-y-2">
                                    <li>
                                        <a class="flex items-center w-full px-3 py-2 text-sm font-semibold rounded text-gray-700 hover:bg-gray-100 hover:text-green-700 transition"
                                            href="{{ route('consumers.dashboard') }}">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ __('Mans profils') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="flex items-center w-full px-3 py-2 text-sm font-semibold rounded text-gray-700 hover:bg-gray-100 hover:text-green-700 transition"
                                            href="{{ route('consumers.dashboard') }}">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ __('Mani pieteikumi') }}
                                        </a>
                                    </li>
                                    <li class="pt-2 border-t border-gray-100">
                                        <form action="{{ route('consumers.logout') }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center w-full px-3 py-2 text-sm font-semibold rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition cursor-pointer"
                                                onclick="return confirm('{{ __('Are you sure you want to log out?') }}')">
                                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                {{ __('Iziet') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                    </div>
                @else
                    <a href="{{ route('consumers.login') }}" aria-label="login"
                        class="flex flex-row bg-[#3FAF4F] hover:bg-green-700 select-none justify-start items-center px-2 gap-1 md:px-4 py-2 text-base font-medium text-center text-white rounded-full md:rounded border border-green-600 leading-tight">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 10.625C7.35837 10.625 5.20837 8.47502 5.20837 5.83335C5.20837 3.19169 7.35837 1.04169 10 1.04169C12.6417 1.04169 14.7917 3.19169 14.7917 5.83335C14.7917 8.47502 12.6417 10.625 10 10.625ZM10 2.29169C8.05004 2.29169 6.45837 3.88335 6.45837 5.83335C6.45837 7.78335 8.05004 9.37502 10 9.37502C11.95 9.37502 13.5417 7.78335 13.5417 5.83335C13.5417 3.88335 11.95 2.29169 10 2.29169Z"
                                fill="white" />
                            <path
                                d="M17.1584 18.9583C16.8167 18.9583 16.5334 18.675 16.5334 18.3333C16.5334 15.4583 13.6002 13.125 10.0002 13.125C6.40012 13.125 3.4668 15.4583 3.4668 18.3333C3.4668 18.675 3.18346 18.9583 2.8418 18.9583C2.50013 18.9583 2.2168 18.675 2.2168 18.3333C2.2168 14.775 5.70846 11.875 10.0002 11.875C14.2918 11.875 17.7834 14.775 17.7834 18.3333C17.7834 18.675 17.5001 18.9583 17.1584 18.9583Z"
                                fill="white" />
                        </svg>
                        <span class="hidden md:block">
                            {{ __('Login') }}
                        </span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</header>
