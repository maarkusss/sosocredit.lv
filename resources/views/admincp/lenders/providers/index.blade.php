@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">

        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">
                {{ $page_title ?? 'Provider Link Clicks - Scope: 2 months' }}
            </h1>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-6">

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-2xl font-bold text-blue-600">{{ $totalVisits }}</div>
                <div class="text-sm text-gray-600">Total Clicks</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-2xl font-bold text-orange-300">{{ $lastMonthVisits }}</div>
                <div class="text-sm text-gray-600">Last Month</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-2xl font-bold text-orange-600">{{ $thisMonthVisits }}</div>
                <div class="text-sm text-gray-600">This Month</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-2xl font-bold text-purple-600">{{ $yesterdayVisits }}</div>
                <div class="text-sm text-gray-600">Yesterday's Clicks</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="text-2xl font-bold text-purple-600">{{ $todayVisits }}</div>
                <div class="text-sm text-gray-600">Today's Clicks</div>
            </div>

        </div>

        <!-- Provider Performance -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Provider Performance</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Provider</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Total Clicks</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Last Month</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">This Month</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Yesterday</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Today</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Sources</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Last Visit</th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">AR %</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($lenders as $lender)
                            @php
                                $ar = $approvalRates[$lender->id] ?? null;
                                $lastVisit = $lastVisits[$lender->id] ?? null;

                                $providerTotal = $lender->total_visits;
                                $providerLastMonth = $lender->last_month_visits;
                                $providerThisMonth = $lender->this_month_visits;
                                $providerYesterday = $lender->yesterday_visits;
                                $providerToday = $lender->today_visits;
                            @endphp

                            @if ($providerTotal > 0)
                                <tr class="hover:bg-gray-50">

                                    {{-- Provider --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($lender->image)
                                                <img class="h-10 pr-4" src="{{ $lender->image }}" alt="{{ $lender->name }}">
                                            @else
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 mr-3 bg-gray-300 rounded-full">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ substr($lender->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $lender->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $lender->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4">
                                        <div class="text-lg font-semibold text-gray-900">{{ $providerTotal }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $totalVisits > 0 ? round(($providerTotal / $totalVisits) * 100, 1) : 0 }}%
                                            of total
                                        </div>
                                    </td>

                                    {{-- Last month --}}
                                    <td class="px-6 py-4">
                                        <div class="text-lg font-semibold text-gray-900">{{ $providerLastMonth }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $lastMonthVisits > 0 ? round(($providerLastMonth / $lastMonthVisits) * 100, 1) : 0 }}%
                                            of total
                                        </div>
                                    </td>

                                    {{-- This month --}}
                                    <td class="px-6 py-4">
                                        <div class="text-lg font-semibold text-gray-900">{{ $providerThisMonth }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $thisMonthVisits > 0 ? round(($providerThisMonth / $thisMonthVisits) * 100, 1) : 0 }}%
                                            of total
                                        </div>
                                    </td>

                                    {{-- Yesterday --}}
                                    <td class="px-6 py-4">
                                        <div class="text-lg font-semibold text-gray-900">{{ $providerYesterday }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $yesterdayVisits > 0 ? round(($providerYesterday / $yesterdayVisits) * 100, 1) : 0 }}%
                                            of total
                                        </div>
                                    </td>

                                    {{-- Today --}}
                                    <td class="px-6 py-4">
                                        <div class="text-lg font-semibold text-gray-900">{{ $providerToday }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $todayVisits > 0 ? round(($providerToday / $todayVisits) * 100, 1) : 0 }}%
                                            of total
                                        </div>
                                    </td>

                                    {{-- Sources --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($sources[$lender->id] ?? [] as $row)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $row->source === 'web' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $row->source === 'email' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $row->source === 'cs' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $row->source === 'wa' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $row->source === 'sms' ? 'bg-purple-100 text-purple-800' : '' }}
                                    ">
                                                    {{ ucfirst($row->source) }}: {{ $row->total }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Last visit --}}
                                    <td class="px-6 py-4">
                                        @if ($lastVisit)
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($lastVisit)->format('M j, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($lastVisit)->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-gray-500">Never</span>
                                        @endif
                                    </td>

                                    {{-- Approval --}}
                                    <td class="px-6 py-4">
                                        @if ($ar)
                                            <div class="text-lg font-semibold text-gray-900">{{ $ar->apr }}%</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $ar->approved }} / {{ $ar->total }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>

                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
