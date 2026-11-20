@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Overview' }}</h1>
        </div>
        <div class="flex flex-col w-full -mx-2 md:flex-row">
            <div class="flex flex-col w-full mx-2 md:w-1/3"
                 id="overview_statistics">
                <div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none bg-white rounded">
                    <div class="flex flex-col">
                        <span class="mb-2 text-3xl font-medium text-primary-normal">{{ $data['commissions']['today'] }}</span>
                        <span class="font-normal text-gray-500 text-normal">Commissions today</span>
                    </div>
                    <div>
                        <span class="text-xl font-medium {{ $data['commissions']['color'] }}">{{ $data['commissions']['percentage'] }}%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none bg-white rounded">
                    <div class="flex flex-col">
                        <span class="mb-2 text-3xl font-medium text-primary-normal">{{ $data['clicks']['today'] }}</span>
                        <span class="font-normal text-gray-500 text-normal">Clicks today</span>
                    </div>
                    <div>
                        <span class="text-xl font-medium {{ $data['clicks']['color'] }}">{{ $data['clicks']['percentage'] }}%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none bg-white rounded md:mb-0">
                    <div class="flex flex-col">
                        <span class="mb-2 text-3xl font-medium text-primary-normal">{{ $data['visitors']['today'] }}</span>
                        <span class="font-normal text-gray-500 text-normal">Visitors today</span>
                    </div>
                    <div>
                        <span class="text-xl font-medium {{ $data['visitors']['color'] }}">{{ $data['visitors']['percentage'] }}%</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col w-full p-4 mx-2 bg-white rounded md:w-2/3">
                <div id="chart"
                     class="h-full">
                </div>
            </div>
        </div>

        @can('view lenders')
            <div class="admin-page-title-wrap mt-5">
                <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Totals' }}</h1>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 w-full gap-1">
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
                                'enabled' => config('app.go_rewrites_enabled') ? 'yes' : 'no',
                                'total' => $data['go_rewrites']['total'] ?? '-',
                            ],
                            'link' => $data['go_rewrites']['link'] ?? '',
                        ])
                    </div>
                @endcan

                <div class="flex flex-col px-2">
                    @include('admincp.includes.dashboard-stat', [
                        'value' => 'Goodwall',
                        'title' => config('goodwall.enabled') ? ' enabled' : ' disabled',
                        'value_color' => 'text-white',
                        'bg_color' => config('goodwall.enabled') ? ' bg-[#60223b] bg-gradient-to-r from-[#60223b] to-[#b11850]' : ' bg-gray-400',
                        'title_color' => 'text-white',
                    ])
                </div>
            </div>
        @endcan
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
