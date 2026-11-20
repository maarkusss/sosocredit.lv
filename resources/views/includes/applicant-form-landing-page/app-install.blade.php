@php
    $image = '/images/app_intro_lv.avif';

    if (app()->getLocale() == 'ru') {
        $image = '/images/app_intro_ru.avif';
    }

    $user_agent = request()->server('HTTP_USER_AGENT');
    $os = get_os($user_agent);
    $os = Illuminate\Support\Str::lower($os);

    $android = App\Models\MobileApp::where('platform', 'Android')->where('active', 1)->first();
    $ios = App\Models\MobileApp::where('platform', 'iOS')->where('active', 1)->first();
    $galaxy = App\Models\MobileApp::where('platform', 'Galaxy')->where('active', 1)->first();

    if (!empty($android->id)) {
        if (isset($android->link_parameters[app()->getLocale()])) {
            $android_link = $android->link . $android->link_parameters[app()->getLocale()] . getMobileParameters();
        } else {
            $android_link = $android->link . getMobileParameters();
        }
    }

    if (!empty($ios->id)) {
        if (isset($ios->link_parameters[app()->getLocale()])) {
            $ios_link = $ios->link . $ios->link_parameters[app()->getLocale()];
        } else {
            $ios_link = $ios->link;
        }
    }

    if (!empty($galaxy->id)) {
        if (isset($galaxy->link_parameters[app()->getLocale()])) {
            $galaxy_link = $galaxy->link . $galaxy->link_parameters[app()->getLocale()];
        } else {
            $galaxy_link = $galaxy->link;
        }
    }

    $showAndroid = (Str::contains($os, 'windows') || Str::contains($os, 'android')) && isset($android_link);
    $showIos = (Str::contains($os, 'mac') || Str::contains($os, 'ios')) && isset($ios_link);
@endphp
<section class="bg-white">
    <div class="max-w-screen-xl px-4 pt-4 mx-auto xl:px-0 lg:pt-16">
        <div class="flex justify-center lg:flex-row flex-col-reverse gap-y-12 lg:pt-0 pt-12 items-center w-full h-full lg:h-[236px] rounded-2xl bg-[#D4E6D6] relative overflow-visible"
            id="app-install">

            <!-- Promo desktop bg -->
            <img class="absolute inset-0 z-0 object-cover w-full h-full select-none rounded-2xl"
                src="{{ asset('/images/cta_app.avif') }}" alt="{{ __('alts.cta_banner') }}" fetchpriority="high" />

            <!-- Intro bilde -->
            <img class="lg:absolute relative lg:right-12 select-none lg:-bottom-[1px] bottom-0 z-10 h-[18.5rem] object-contain"
                src="{{ asset($image) }}" alt="{{ __('alts.cta_banner') }}" fetchpriority="high" width="236" height="298" />

            <div class="relative z-20 flex flex-col items-center justify-between w-full px-4 lg:flex-row lg:px-8">
                <div class="flex flex-col items-center justify-center gap-6 mx-4 lg:items-start lg:justify-start">
                    <div class="flex flex-col justify-start gap-2 lg:items-start">
                        <p
                            class="px-6 text-xl font-bold leading-7 text-center lg:px-0 lg:text-3xl lg:leading-9 text-neutral-900 lg:text-left">
                            @lang('Install our app')
                        </p>
                        <p
                            class="self-stretch justify-center hidden px-2 text-sm font-medium leading-tight text-center lg:px-0 lg:text-start text-neutral-900 lg:text-base lg:block">
                            @lang('Scan the QR code with your phone camera and follow the link')
                        </p>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-6 select-none sm:flex-row">
                        {{-- Apple store --}}
                        @if ($showIos)
                            <a class="opacity-100 cursor-allowed" href="{{ $ios_link ?? '' }}" target="_blank">
                            <img src="/app-store-black.svg" alt="App store icon" class="shrink-0 max-h-[55px]" width="202" height="55"
                                 draggable="false">
                            </a>
                        @else
                            <div class="opacity-25 cursor-not-allowed">
                                <img src="/app-store-black.svg" alt="App store icon" class="shrink-0 max-h-[55px]" width="202" height="55"
                                     draggable="false">
                            </div>
                        @endif

                        {{-- Google play --}}
                        <div id="google-play-icon-qr">
                            @if($showAndroid)
                                <a class="opacity-100 cursor-allowed" href="{{ $android_link ?? '' }}" target="_blank">
                                    <img src="/google-play-black.svg" alt="Google play icon" class="shrink-0 max-h-[55px]" width="202" height="55"
                                        draggable="false">
                                </a>
                            @else
                                <div class="opacity-25 cursor-not-allowed">
                                    <img src="/google-play-black.svg" alt="App store icon" class="shrink-0 max-h-[55px]" width="202" height="55"
                                         draggable="false">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="relative hidden mb-10 w-max max-w-[250px] select-none lg:block mr-80">
                    <div
                        class="flex items-center justify-center gap-4 p-2 bg-white drop-shadow-[2px_2px_14px_rgba(0,0,0,0.20)] rounded-2xl">
                        <img src="{{ asset('qr-code.svg') }}" alt="" class="w-[100px] h-auto" width="100" height="100">
                        <span class="italic font-semibold text-primary-dark">@lang('Scan and download')</span>
                    </div>

                    <img src="{{ asset('/images/cta_arrow.avif') }}" alt=""
                        class="absolute -right-12 -bottom-18">
                </div>
            </div>
        </div>
    </div>
</section>
