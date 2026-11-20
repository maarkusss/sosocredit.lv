@php

    $meta_title = config('app.name');
    $meta_description = '';
    $meta_keywords = '';

    if (!empty($meta) && !empty($meta['title'])) {
        $meta_title = $meta['title'];
    }

    if (!empty($meta) && !empty($meta['description'])) {
        $meta_description = $meta['description'];
    }

    if (!empty($meta) && !empty($meta['keywords'])) {
        $meta_keywords = $meta['keywords'];
    }

    if (empty($meta_description)) {
        $meta_description = $meta_title;
    }

@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        @hasSection('title')@yield('title')@else{{ $meta_title }}@endif
    </title>

    <meta name="description" content="{{ $meta_description }}">
    @if (!empty($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif
    @if (!app()->isProduction())
        <meta name="robots" content="noindex, nofollow">
    @elseif (!empty($meta_robots))
        <meta name="robots" content="{{ $meta_robots }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (!empty($is_applicant_page) || config('app.name') === 'Sosocredit.lv')
        <link rel="shortcut icon" href="/favicon2.png">
    @else
        <link rel="shortcut icon" href="/favicon.png">
    @endif

    @if (!empty($canonical_href))
        <link rel="canonical" href="{{ $canonical_href }}" />
    @endif

    <!-- Preconnect critical font origins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Preload the Google Fonts CSS without blocking render -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    </noscript>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    @yield('head')
    @if ($head_code = \App\Models\Setting::retrieve('head_code'))
        {!! $head_code !!}
    @endif

    <script type="application/ld+json">
        {!! json_encode([
          "@context" => "https://schema.org",
          "@type" => "BreadcrumbList",
          "itemListElement" => generate_breadcrumbs()
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    @if (isset($extra_source) && $extra_source)
        {!! $extra_source !!}
    @endif

    @if (isset($section) && ($section->template === 'home' || $section->template === 'applicant_form_landing_page'))
        {!! $section->source !!}
        <link rel="prefetch" href="{{ asset('images/target.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/discount.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/application.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/approved.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/pointer_click.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/group.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/discount1.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/24-hours.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/website.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/line.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/to-do-list.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/line_vertical.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/contract.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/wallet.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/form.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/credit-card.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/thunder.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/graph.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/shopping-list.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/cc.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/pie-chart.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/refresh.svg') }}" as="image" type="image/svg+xml">
        <link rel="prefetch" href="{{ asset('images/coins.svg') }}" as="image" type="image/svg+xml">
    @endif
</head>

<body class="body-class public-section {{ config('app.public_css_class') }}"
    :class="{ 'overflow-hidden': (!rightsOfUsageModalClosed || !recieveOffersModalClosed || !privacyPolicyModalClosed) }"
    x-data="{
        rightsOfUsageModalClosed: true,
        rightsOfUsageChecked: false,
        recieveOffersModalClosed: true,
        formEditModalClosed: true,
        recieveOffersChecked: @if (!empty(old('subscribe', !empty($application) ? $application->subscribed : ''))) true @else false @endif,
        privacyPolicyModalClosed: true,
        privacyPolicyChecked: @if (!empty(old('agree_to_terms', !empty($application) ? $application->agree_to_terms : ''))) true @else false @endif,
        formProcessing: false
    }"
    data-config="{{ json_encode([
        'apply_phone_mask' => config('landing_form.apply_phone_mask'),
        'phone_extension' => '+' . phone_extension(),
        'phone_length' => config('landing_form.phone_placeholder') ? strlen(config('landing_form.phone_placeholder')) : 0,
        'require_phone' => config('landing_form.require_phone'),
        'require_email' => config('landing_form.require_email'),
        'verify_phone' => config('app.verify_phone'),
        'verify_email' => config('app.verify_email'),
    ]) }}">
    @if (empty($hide_header))
        @include('includes.header')
    @endif

    @yield('content')

    @if (!empty($qr_popup))
        @include('mobile-apps::includes.app-install_android')
        @include('mobile-apps::includes.app-install_ios')
    @endif

    @if (empty($hide_footer))
        @include('includes.footer', [
            'footer_loan_types' => $applicant_form_types ?? null,
            'show_important_info' => $show_important_info ?? false,
        ])
    @endunless

    @if (empty($hide_cookies))
        @if (config('app.show_cookies_notification') === true)
            @include('includes.cookies-notification')
        @endif
    @endif

    @yield('javascript')
</body>

</html>
