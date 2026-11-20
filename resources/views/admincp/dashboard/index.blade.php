@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        @can('view statistics')
            <div class="admin-page-title-wrap">
                <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Overview' }}</h1>
            </div>
            <div class="flex flex-col w-full -mx-2 md:flex-row">
                <div class="flex flex-col w-full px-2 mx-1 md:w-1/3" id="overview_statistics">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Commissions today',
                        'value' => $data['commissions']['today'],
                        'detail' => $data['commissions']['percentage'] . '%',
                        'detail_color' => $data['commissions']['color'],
                    ])

                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Clicks today',
                        'value' => $data['clicks']['today'],
                        'detail' => $data['clicks']['percentage'] . '%',
                        'detail_color' => $data['clicks']['color'],
                    ])

                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Visitors today',
                        'value' => $data['visitors']['today'],
                        'detail' => $data['visitors']['percentage'] . '%',
                        'detail_color' => $data['visitors']['color'],
                    ])
                </div>
                <div class="flex flex-col w-full px-2 mx-1 mb-3 bg-white rounded md:w-2/3">
                    <div id="chart" class="h-full">
                    </div>
                </div>
            </div>
        @endcan

        @can('view consumers')
            <div class="px-1">
                <div class="mb-4 text-lg font-semibold text-slate-800">Consumers</div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    {{-- All time --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">All time confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['all']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['all']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['all']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['all']['pct'] }}%
                        </div>
                    </div>

                    {{-- Last month --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">Last month confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['last_month']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['last_month']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['last_month']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['last_month']['pct'] }}%
                        </div>
                    </div>

                    {{-- This month --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">This month confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['this_month']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['this_month']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['this_month']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['this_month']['pct'] }}%
                        </div>
                    </div>

                    {{-- Last 7 days --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">Last 7 days confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['weekly']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['weekly']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['weekly']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['weekly']['pct'] }}%
                        </div>
                    </div>

                    {{-- Yesterday --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">Yesterday confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['yesterday']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['yesterday']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['yesterday']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['yesterday']['pct'] }}%
                        </div>
                    </div>

                    {{-- Today --}}
                    <div class="py-2 px-3 rounded-md bg-white min-w-[220px]">
                        <div class="mb-1 text-xs text-gray-600">Today confirmed</div>
                        <div class="text-lg font-medium leading-tight">
                            {{ number_format($consumers_stats['daily']['confirmed']) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            out of <b>{{ number_format($consumers_stats['daily']['total']) }}</b>
                        </div>
                        <div class="text-xs font-medium {{ $consumers_stats['daily']['pct_text_class'] }}">
                            Success rate: {{ $consumers_stats['daily']['pct'] }}%
                        </div>
                    </div>
                </div>
            </div>
        @endcan



        <div class="px-1 mt-5 admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Totals' }}</h1>
        </div>

        <div class="grid w-full grid-cols-1 gap-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            @can('view lenders')
                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Active Lenders',
                        'value' => $data['lenders']['active'] ?? '-',
                        'value_color' => 'text-gray-600',
                        'details_array' => [
                            'total' => $data['lenders']['total'] ?? '-',
                        ],
                        'link' => $data['lenders']['link'] ?? '',
                    ])
                </div>

                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Active Campaigns',
                        'value' => $data['lendercampaigns']['active'] ?? '-',
                        'value_color' => 'text-gray-600',
                        'details_array' => [
                            'total' => $data['lendercampaigns']['total'] ?? '-',
                        ],
                        'link' => $data['lendercampaigns']['link'] ?? '',
                    ])
                </div>

                @can('view go-rewrites')
                    <div class="flex flex-col px-2">
                        @include('admincp.includes.dashboard-stat', [
                            'title' => 'Active Go Rewrites',
                            'value' => $data['go_rewrites']['active'] ?? '-',
                            'value_color' => 'text-gray-600',
                            'details_array' => [
                                'total' => $data['go_rewrites']['total'] ?? '-',
                            ],
                            'link' => $data['go_rewrites']['link'] ?? '',
                        ])
                    </div>
                @endcan

                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Active Lender Filters',
                        'value' => $data['lenderfilters']['active'] ?? '-',
                        'value_color' => 'text-gray-600',
                        'details_array' => [
                            'total' => $data['lenderfilters']['total'] ?? '-',
                        ],
                        'link' => $data['lenderfilters']['link'] ?? '',
                    ])
                </div>
            @endcan

            @can('view loantypes')
                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Active Loantypes',
                        'value' => $data['loantypes']['active'] ?? '-',
                        'value_color' => 'text-gray-600',
                        'details_array' => [
                            'total' => $data['loantypes']['total'] ?? '-',
                        ],
                        'link' => $data['loantypes']['link'] ?? '',
                    ])
                </div>
            @endcan

            @can('access postbacks')
                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Active Postbacks',
                        'value' => $data['postbacks']['active'] ?? '-',
                        'value_color' => 'text-gray-600',
                        'details_array' => [
                            'enabled' => config('postbacks.enabled') ? 'yes' : 'no',
                            'total' => $data['postbacks']['total'] ?? '-',
                        ],
                        'link' => $data['postbacks']['link'] ?? '',
                    ])
                </div>
            @endcan

            @can('view consumers')
                <div class="flex flex-col px-2">
                    <div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none bg-white rounded">
                        <div class="relative flex flex-col">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="mb-3 w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>

                            @if (config('landing_form.require_phone'))
                                <span class="absolute top-0 text-gray-500 left-8 text-xxs">Collected</span>
                            @else
                                <span class="absolute top-0 text-orange-400 left-8 text-xxs">Not collected</span>
                            @endif

                            <span class="font-normal text-gray-500 text-normal">Consumer phones</span>
                        </div>
                        <div class="h-full text-right">
                            <p class="mb-1">
                                @if (config('app.verify_phone'))
                                    <span class="px-1 py-0 mr-0 badge badge-success">Verified in WEB</span>
                                @else
                                    <span class="px-1 py-0 mr-0 badge badge-warning">Not verified in WEB</span>
                                @endif
                            </p>
                            <p class="mb-1">
                                @if (config('app.verify_phone_in_api'))
                                    <span class="px-1 py-0 mr-0 badge badge-success">Verified in API</span>
                                @else
                                    <span class="px-1 py-0 mr-0 badge badge-warning">Not verified in API</span>
                                @endif
                            </p>

                            <p class="mb-0">
                                <span
                                    class="badge mr-0 px-1 {{ config('hoi_fm.export_phone_numbers') ? '' : 'badge-warning' }}">
                                    <a href="https://hoi.fm" target="_blank">
                                        <img src="/images/hoi.png" title="Hoi.fm" alt="hoi.fm"
                                            style="display: inline; width: 10px; margin-bottom: 1px; filter: invert(.8)" />
                                    </a>
                                    Hoi export: {{ config('hoi_fm.export_phone_numbers') ?: 'none' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col px-2">
                    <div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none bg-white rounded">
                        <div class="relative flex flex-col">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="mb-3 w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>

                            @if (config('landing_form.require_email'))
                                <span class="absolute top-0 text-gray-500 left-9 text-xxs">Collected</span>
                            @else
                                <span class="absolute top-0 text-orange-400 left-9 text-xxs">Not collected</span>
                            @endif

                            <span class="font-normal text-gray-500 text-normal">Consumer emails</span>
                        </div>
                        <div class="h-full text-right">
                            <p class="mb-2">
                                @if (config('app.verify_email'))
                                    <span class="px-1 py-0 mr-0 badge badge-success">Verified</span>
                                @else
                                    <span class="px-1 py-0 mr-0 badge badge-warning">Not verified</span>
                                @endif
                            </p>

                            <p class="mb-0">
                                <span class="badge mr-0 px-1 {{ config('hoi_fm.export_emails') ? '' : 'badge-warning' }}">
                                    <a href="https://hoi.fm" target="_blank">
                                        <img src="/images/hoi.png" title="Hoi.fm" alt="hoi.fm"
                                            style="display: inline; width: 10px; margin-bottom: 1px; filter: invert(.8)" />
                                    </a>
                                    Hoi export: {{ config('hoi_fm.export_emails') ?: 'none' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="flex flex-col px-2">
                @include('admincp.includes.dashboard-stat', [
                    'value' => 'Goodwall',
                    'title' => config('goodwall.enabled') ? ' enabled' : ' disabled',
                    'value_color' => 'text-white',
                    'bg_color' => config('goodwall.enabled')
                        ? ' bg-[#60223b] bg-gradient-to-r from-[#60223b] to-[#b11850]'
                        : ' bg-gray-400',
                    'title_color' => 'text-white',
                ])
            </div>

            @if (!app()->isProduction())
                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'value' => 'Outline VPN',
                        'title' =>
                            config('goodwall.enabled') && !app()->isProduction()
                                ? ' restricted frontend'
                                : ' disabled',
                        'value_color' => 'text-white',
                        'bg_color' =>
                            config('goodwall.enabled') && !app()->isProduction()
                                ? ' bg-[#1e493a] bg-gradient-to-r from-[#1e493a] to-[#778d84]'
                                : ' bg-gray-400',
                        'title_color' => 'text-white',
                        'detail_container_class' => 'leading-none text-xs',
                        'detail_color' => 'text-teal-900',
                    ])
                </div>
            @endif

            @can('viewInternals')
                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'value' => 'Hoi.fm',
                        'title' => config('services.hoi_fm.user_token') ? ' connected' : ' not connected',
                        'value_color' => 'text-white',
                        'bg_color' => config('services.hoi_fm.user_token')
                            ? ' bg-[#6071e4] bg-gradient-to-r from-[#6071e4] to-[#815fe4]'
                            : ' bg-gray-400',
                        'title_color' => 'text-white',
                        'detail_container_class' => 'leading-none font-mono',
                        'detail_color' => 'text-teal-900',
                        'details_array' => [
                            'email' => config('services.hoi_fm.email_project_token') ? 'YES' : 'not',
                            'push' => config('services.hoi_fm.push_project_token') ? 'YES' : 'not',
                            'sms' => config('services.hoi_fm.sms_project_token') ? 'YES' : 'not',
                        ],
                    ])
                </div>

                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Environment',
                        'value' => ucfirst(
                            strlen(config('app.env')) < 6 ? config('app.env') : substr(config('app.env'), 0, 4)),
                        'value_color' => 'text-gray-600',
                        'detail_container_class' => 'leading-none',
                        'details_array' => [
                            'sms gateway' => sms_gateway_name(),
                            'sms alpha name' => config('app.sms_alpha_name'),
                            'currency' => currency_symbol(),
                            'ver.' => app()->version() . ' | ' . PHP_VERSION,
                        ],
                    ])
                </div>

                <div class="flex flex-col px-2">
                    @php
                        $debug_links = [];
                        $debug_links[''] =
                            '<a href="/en/log-viewer" target="_blank" class="px-1 mr-0 badge">Log Viewer</a>';
                        $debug_links[' '] =
                            '<a href="/telescope" target="_blank" class="px-1 mr-0 badge">Telescope</a>';
                    @endphp

                    @include('admincp.includes.dashboard-stat', [
                        'title' => 'Debug tools',
                        'value' => 'Debug',
                        'value_color' => 'text-gray-600',
                        'details_array' => $debug_links,
                    ])
                </div>
            @endcan
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        let chartHeight = document.querySelector("#overview_statistics").clientHeight;

        let options = {
            chart: {
                type: "area",
                toolbar: {
                    tools: {
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        reset: false,
                        pan: false
                    }
                },
                height: chartHeight,
                maintainAspectRatio: false,
                responsive: true,
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                    name: "Commissions",
                    data: [{!! implode(', ', $chart_data['commissions']) !!}]
                },
                {
                    name: "Clicks",
                    data: [{!! implode(', ', $chart_data['clicks']) !!}]
                },
                {
                    name: "Visitors",
                    data: [{!! implode(', ', $chart_data['visitors']) !!}]
                },

            ],
            xaxis: {
                categories: ["{!! implode('", "', array_keys($chart_data['clicks'])) !!}"]
            }
        }
    </script>
@endsection
