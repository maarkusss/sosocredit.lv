<!DOCTYPE HTML>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <meta name="robots"
          content="noindex, nofollow">
    <style>
        .body {
            background: #f7fafc;
            margin: 0;
            padding: 15px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }

        .logo {
            text-align: center;
            margin: 0 0 15px 0;
        }

        .title {
            text-align: center;
            font-size: 1rem;
            color: #2d3748;
            font-weight: 600;
        }

        .lender-box-outside {
            width: 25%;
            float: left;
            overflow: hidden;
            position: relative;
        }

        .lender-box-inside {
            margin: 0 15px 15px 0;
            text-align: center;
            padding: 10px;
            background: #FFF;
            border-radius: .5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
            border: 1px solid #e2e8f0;
        }

        .lender-box-inside-primary {
            border: 1px solid #48bb78;
        }

        .lender-slogan {
            position: absolute;
            left: 0;
            top: 0;
            font-size: .875rem;
            color: #2d3748;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
            padding: .25rem;
            line-height: 1;
            font-weight: 600;
            border-radius: .25rem;
            background-color: #e2e8f0;
            text-align: center;
            max-height: 14px;
        }

        .lender-slogan-primary {
            background-color: #48bb78;
            color: #FFF;
        }

        .lender-grid {
            overflow: hidden;
        }

        .lender-left {
            float: left;
            width: 50%;
        }

        .lender-right {
            float: left;
            width: 50%;
        }

        .lender-left .lender-title,
        .lender-right .lender-title {
            font-size: .875rem;
            color: #718096;
            line-height: 1;
            font-weight: 400;
            text-align: left;
        }

        .lender-left .lender-text,
        .lender-right .lender-text {
            font-size: 1.125rem;
            color: #2d3748;
            line-height: 1;
            font-weight: 600;
            text-align: left;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .lender-text-primary {
            color: #ff7473;
        }

        .lender-logo-container {
            margin-bottom: 1rem;
            height: 3rem;
            margin-top: 15px;
        }

        .lender-logo-img {
            max-width: 100%;
            max-height: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        .lender-button {
            border-radius: .5rem;
            font-size: 1rem;
            color: #ffffff !important;
            text-align: center;
            padding: 15px 0;
            display: block;
            line-height: 1;
            font-weight: 600;
            background-color: #ff7473;
            text-decoration: none;
        }

        .clear {
            clear: both;
        }

        .h4 {
            font-size: 1.125rem;
            color: #2d3748;
            margin-top: 1rem;
            line-height: 1;
            font-weight: 600;
            margin-bottom: 0;
        }

        .p {
            font-size: 1rem;
            color: #718096;
            margin-top: .5rem;
            line-height: 1.25;
            font-weight: 400;
        }

        .p a {
            color: #718096 !important;
            text-decoration: underline !important;
        }

        .unsubscribe {
            padding-top: 10px;
            border-top: 1px solid #dcdcdc;
            text-align: center;
        }

        @media screen and (max-device-width: 1100px),
        screen and (max-width: 1100px) {
            .lender-box-outside {
                width: 33%;
            }
        }

        @media screen and (max-device-width: 720px),
        screen and (max-width: 720px) {
            .lender-box-outside {
                width: 50%;
            }
        }

        @media screen and (max-device-width: 500px),
        screen and (max-width: 500px) {
            .lender-box-outside {
                width: 100%;
            }

            .lender-box-inside {
                margin-right: 0;
            }
        }

    </style>
</head>

<body class="body">
    <div class="logo">
        <a href="{{ config('app.url') }}"
           target="_blank">
            <img src="{{ config('app.url') }}/logo.png?v1"
                 width="96"
                 height="24"
                 alt="{{ config('app.name') }} logo">
        </a>
    </div>
    <p class="title">@lang('main.fill_enquiry_wait_and_get_money')</p>
    @php
        $i = 0;
    @endphp
    @foreach ($lenders as $lender)
        @php
            $i++;
            $lenders_data = \App\Models\Lender\LenderData::query()->where('lender_id', $lender->id)
                ->where('lang', app()->getLocale())
                ->first();
            $slogan_medal_arr = ['🥇', '🥈', '🥉'];
        @endphp
        <div class="lender-box-outside">
            <div class="lender-box-inside{{ $i <= 3 ? ' lender-box-inside-primary' : '' }}">
                <div class="lender-slogan{{ $i <= 3 ? ' lender-slogan-primary' : '' }}">
                    {{ $i <= 3 ? $slogan_medal_arr[$i - 1] : '' }}
                    {{ $lenders_data ? $lenders_data->slogan : '' }}
                </div>
                <div class="lender-logo-container">
                    <a href="{{ route('go', ['lender_id' => $lender->id, 'lender_position' => $i]) }}?utm_source={{ $user->utm_source }}&utm_medium={{ $user->utm_medium }}&utm_campaign={{ $user->utm_campaign }}&utm_content={{ $user->utm_content }}&gclid={{ $user->gclid }}"
                       rel="nofollow"
                       target="_blank"><img class="lender-logo-img"
                             src="{{ config('app.url') }}{{ $lender->image }}"
                             alt="{{ config('app.name') }}"></a>
                </div>
                <div class="lender-grid">
                    <div class="lender-left">
                        <div class="lender-title">@lang('main.credit')</div>
                        <div class="lender-text">{!! $lender->zero_percent ? '<span class="lender-text-primary">0%</span> ' : '' !!} @lang('main.till') {{ $lender->first_loan }}@lang('money.euro')</div>
                    </div>
                    <div class="lender-right">
                        <div class="lender-title">@lang('main.sum')</div>
                        <div class="lender-text">@lang('main.max') {{ $lender->max_amount }}@lang('money.euro')</div>
                    </div>
                    <div class="clear"></div>
                    <div class="lender-left">
                        <div class="lender-title">@lang('main.age')</div>
                        <div class="lender-text">{{ $lender->min_years }} @lang('main.till') {{ $lender->max_years }}</div>
                    </div>
                </div>
                <a class="lender-button"
                   href="{{ route('go', ['lender_id' => $lender->id, 'lender_position' => $i]) }}?utm_source={{ $user->utm_source }}&utm_medium={{ $user->utm_medium }}&utm_campaign={{ $user->utm_campaign }}&utm_content={{ $user->utm_content }}&gclid={{ $user->gclid }}"
                   rel="nofollow"
                   target="_blank">@lang('main.submit_enquiry')</a>
            </div>
        </div>
    @endforeach
    <div class="clear"></div>

    <h4 class="h4">@lang('offers.line1')</h4>
    <p class="p">- @lang('offers.line2')</p>
    <p class="p">- @lang('offers.line3')</p>
    <p class="p">- @lang('offers.line4')</p>
    <p class="p">- @lang('offers.line5')</p>
    <p class="p">- @lang('offers.line6')</p>

    <h4 class="h4">@lang('offers.line7')</h4>
    <p class="p">@lang('offers.line8')</p>

    <h4 class="h4">@lang('offers.line9')</h4>
    <p class="p">@lang('offers.line10')</p>

    <h4 class="h4">@lang('offers.line11')</h4>
    <p class="p">@lang('offers.line12')</p>

    <h4 class="h4">@lang('offers.line13')</h4>
    <p class="p">@lang('offers.line14')</p>

    <h4 class="h4">@lang('offers.line15')</h4>
    <p class="p">@lang('offers.line16')</p>

    <p class="p unsubscribe">@lang('main.if_you_do_not_want_news_via_email_click')
        <a href="@if (!empty($unsubscribe_link)){{ $unsubscribe_link }}@else{{ route('unsubscribe.email') }}@endif"
           target="_blank">@lang('main.here')</a>.
    </p>
</body>

</html>
