@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Cookie consents' }}</h1>
        </div>

        {{-- CONSENT STATS CHARTS --}}
        @php
            $total = (int) $stats->total;
            $analyticsYes = (int) $stats->analytics_yes;
            $marketingYes = (int) $stats->marketing_yes;
            $functionalYes = (int) $stats->functional_yes;
        @endphp

        <div class="flex flex-wrap gap-4 mb-5">
            @foreach ([['label' => 'Analytics', 'yes' => $analyticsYes, 'no' => $total - $analyticsYes], ['label' => 'Marketing', 'yes' => $marketingYes, 'no' => $total - $marketingYes], ['label' => 'Functional', 'yes' => $functionalYes, 'no' => $total - $functionalYes]] as $stat)
                <div class="flex items-center gap-4 p-4 bg-white rounded shadow-sm" style="min-width:200px">
                    <div class="relative" style="width:72px;height:72px">
                        <canvas id="chart-{{ Str::lower($stat['label']) }}" width="72" height="72"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-sm font-bold leading-tight text-gray-800">
                                {{ $total > 0 ? round(($stat['yes'] / $total) * 100) : 0 }}%
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ $stat['label'] }}
                        </div>
                        <div class="flex items-center gap-1 text-xs text-green-700">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                            Yes: <strong>{{ $stat['yes'] }}</strong>
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                            <span class="inline-block w-2 h-2 bg-gray-200 rounded-full"></span>
                            No: <strong>{{ $stat['no'] }}</strong>
                        </div>
                        <div class="text-xs text-gray-300 mt-0.5">Total: {{ $total }}</div>
                    </div>
                </div>
            @endforeach

            {{-- Applications last 6 months --}}
            <div class="flex items-center gap-4 p-4 bg-white rounded shadow-sm" style="min-width:680px">
                <div style="width:200px;height:72px">
                    <canvas id="chart-applications" class="h-auto" width="200" height="72"></canvas>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Applications</div>
                    <div class="text-xs text-gray-400">Last 6 months</div>
                    <div class="text-sm font-bold text-gray-800 mt-0.5">
                        {{ array_sum($monthCounts) }} total
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full p-4 bg-white rounded">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                        <tr class="border-b">
                            <th class="table__column">Visitor</th>
                            <th class="table__column">IP Address</th>
                            <th class="table__column">Source / Medium</th>
                            <th class="table__column">Campaign</th>
                            <th class="table__column">From / To</th>
                            <th class="table__column">User Agent</th>
                            <th class="table__column">Consents</th>
                            <th class="table__column">Commissions</th>
                            <th class="table__column">Consented At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cookies as $cookie)
                            @php $visitor = $visitors[$cookie->visitor_id] ?? null; @endphp
                            <tr class="table__row">
                                <td class="table__cell">
                                    <div class="flex flex-col items-center">
                                        @if ($cookie->visitor_id)
                                            <button type="button" class="font-medium hover:underline"
                                                onclick="openVisitorModal({{ $cookie->visitor_id }})">
                                                {{ $cookie->visitor_id }}
                                            </button>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                        <div class="text-xs">
                                            Commission:
                                            @if ($cookie->visitor_id && isset($withCommission[$cookie->visitor_id]))
                                                <span class="inline-flex items-center text-xs font-medium text-green-700">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Yes
                                                </span>
                                            @else
                                                <span class="inline-block text-xs font-medium text-gray-400">No</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="text-sm table__cell">{{ $cookie->ip_address ?? '—' }}</td>

                                {{-- Source / Medium --}}
                                <td class="text-sm table__cell">
                                    @if ($visitor && ($visitor->utm_source || $visitor->utm_medium))
                                        <div>{{ $visitor->utm_source ?? '—' }}</div>
                                        <div class="text-xs text-gray-400">{{ $visitor->utm_medium ?? '' }}</div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Campaign --}}
                                <td class="text-sm table__cell">
                                    {{ $visitor?->utm_campaign ?? '—' }}
                                </td>

                                {{-- From / To --}}
                                <td class="text-sm table__cell" style="max-width: 200px;">
                                    @if ($visitor && ($visitor->from || $visitor->to))
                                        <div class="flex flex-col gap-1">
                                            <input type="text" readonly value="{{ $visitor->from ?? '' }}"
                                                placeholder="—"
                                                class="w-full px-2 py-1 text-xs text-gray-700 border border-gray-200 rounded bg-gray-50 cursor-text focus:outline-none focus:border-gray-400"
                                                style="min-width:140px" title="{{ $visitor->from ?? '' }}" />
                                            <input type="text" readonly value="{{ $visitor->to ?? '' }}" placeholder="—"
                                                class="w-full px-2 py-1 text-xs text-gray-500 border border-gray-200 rounded bg-gray-50 cursor-text focus:outline-none focus:border-gray-400"
                                                style="min-width:140px" title="{{ $visitor->to ?? '' }}" />
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- User Agent --}}
                                <td class="text-sm table__cell" style="max-width: 160px;">
                                    @if ($visitor?->user_agent)
                                        <span class="block truncate" title="{{ $visitor->user_agent }}">
                                            {{ Str::limit($visitor->user_agent, 35) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Consents: Analytics / Marketing / Functional combined --}}
                                <td class="table__cell">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center justify-center gap-1 text-xs">
                                            @if ($cookie->analytics)
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                <span class="font-medium text-green-700">Analytics</span>
                                            @else
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                <span class="text-gray-400">Analytics</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-center gap-1 text-xs">
                                            @if ($cookie->marketing)
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                <span class="font-medium text-green-700">Marketing</span>
                                            @else
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                <span class="text-gray-400">Marketing</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-center gap-1 text-xs">
                                            @if ($cookie->functional)
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                <span class="font-medium text-green-700">Functional</span>
                                            @else
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                <span class="text-gray-400">Functional</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Commissions --}}
                                <td class="table__cell">
                                    @php
                                        $rowCommissions =
                                            $cookie->visitor_id && isset($visitorCommissions[$cookie->visitor_id])
                                                ? $visitorCommissions[$cookie->visitor_id]
                                                : collect();
                                    @endphp
                                    @if ($rowCommissions->isNotEmpty())
                                        <div class="flex flex-col gap-0.5">
                                            @foreach ($rowCommissions as $commission)
                                                <div class="text-xs">
                                                    <span
                                                        class="font-medium text-gray-800">{{ $commission->commission }}</span>
                                                    <span class="ml-1 text-gray-400">{{ $commission->status }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="text-sm text-gray-500 table__cell">
                                    {{ $cookie->created_at?->format('d.m.Y H:i') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-6 text-center text-gray-400 table__cell">No consent records
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $cookies->links('admincp.includes.pagination') }}
            </div>
        </div>
    </div>

    {{-- VISITOR MODAL --}}
    <div id="visitor-modal" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black/40">
        <div class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-base font-semibold text-gray-800" id="modal-title">Visitor</h2>
                <button type="button" onclick="closeVisitorModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modal-body" class="px-5 py-4 space-y-5"></div>
        </div>
    </div>

    <script>
        function openVisitorModal(visitorId) {
            const modal = document.getElementById('visitor-modal');
            const body = document.getElementById('modal-body');
            const title = document.getElementById('modal-title');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            title.textContent = 'Visitor #' + visitorId;
            body.innerHTML = '<div class="flex items-center justify-center py-10 text-sm text-gray-400">Loading...</div>';

            fetch('{{ route('admincp.cookies.visitor', ['visitorId' => '__ID__']) }}'.replace('__ID__', visitorId), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => renderModal(data))
                .catch(() => {
                    body.innerHTML = '<p class="text-sm text-red-500">Failed to load data.</p>';
                });
        }

        function closeVisitorModal() {
            document.getElementById('visitor-modal').classList.replace('flex', 'hidden');
        }

        document.getElementById('visitor-modal').addEventListener('click', function(e) {
            if (e.target === this) closeVisitorModal();
        });

        const badge = val => val ?
            '<span class="inline-block px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Yes</span>' :
            '<span class="inline-block px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">No</span>';

        const row = (label, value) =>
            `<tr>
                <td class="py-1.5 pr-6 text-xs text-gray-400 whitespace-nowrap align-top">${label}</td>
                <td class="py-1.5 text-sm text-gray-800 break-all">${value ?? '—'}</td>
            </tr>`;

        const section = (title, content) =>
            `<div>
                <h3 class="mb-2 text-xs font-semibold tracking-wide text-gray-400 uppercase">${title}</h3>
                ${content}
            </div>`;

        function renderModal(data) {
            const body = document.getElementById('modal-body');
            let html = '';

            // Consumer
            const c = data.consumer;
            html += section('Consumer', c ?
                `<table class="w-full text-left">
                    ${row('Name', `${c.first_name ?? ''} ${c.last_name ?? ''}`.trim() || null)}
                    ${row('Email', c.email)}
                    ${row('Phone', c.phone)}
                    ${row('Email verified', c.email_verified_at ? c.email_verified_at : badge(false))}
                    ${row('Subscribed', badge(c.subscribed))}
                    ${row('Registered', c.created_at)}
                   </table>` :
                '<p class="text-sm text-gray-400">No consumer found.</p>'
            );

            // Clicks
            const clicks = data.clicks ?? [];
            html += section(`Clicks (${clicks.length})`, clicks.length ?
                `<div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead><tr class="border-b">
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Date</th>
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Source</th>
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Campaign</th>
                            <th class="pb-1 text-xs font-medium text-gray-400">Lender</th>
                        </tr></thead>
                        <tbody>${clicks.map(cl => `
                                                                <tr class="border-t">
                                                                    <td class="py-1.5 pr-4 text-sm whitespace-nowrap">${cl.created_at ?? '—'}</td>
                                                                    <td class="py-1.5 pr-4 text-sm">${cl.utm_source ?? '—'}</td>
                                                                    <td class="py-1.5 pr-4 text-sm">${cl.utm_campaign ?? '—'}</td>
                                                                    <td class="py-1.5 text-sm">${cl.lender ?? '—'}</td>
                                                                </tr>`).join('')}
                        </tbody>
                    </table>
                   </div>` :
                '<p class="text-sm text-gray-400">No clicks found.</p>'
            );

            // Commissions
            const commissions = data.commissions ?? [];
            html += section(`Commissions (${commissions.length})`, commissions.length ?
                `<div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead><tr class="border-b">
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Date</th>
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Amount</th>
                            <th class="pb-1 pr-4 text-xs font-medium text-gray-400">Status</th>
                            <th class="pb-1 text-xs font-medium text-gray-400">Lender</th>
                        </tr></thead>
                        <tbody>${commissions.map(cm => `
                                                                <tr class="border-t">
                                                                    <td class="py-1.5 pr-4 text-sm whitespace-nowrap">${cm.created_at ?? '—'}</td>
                                                                    <td class="py-1.5 pr-4 text-sm">${cm.commission ?? '—'}</td>
                                                                    <td class="py-1.5 pr-4 text-sm">${cm.status ?? '—'}</td>
                                                                    <td class="py-1.5 text-sm">${cm.lender ?? '—'}</td>
                                                                </tr>`).join('')}
                        </tbody>
                    </table>
                   </div>` :
                '<p class="text-sm text-gray-400">No commissions found.</p>'
            );

            body.innerHTML = html;
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Doughnut charts
            const charts = [{
                    id: 'chart-analytics',
                    yes: {{ $analyticsYes }},
                    no: {{ $total - $analyticsYes }}
                },
                {
                    id: 'chart-marketing',
                    yes: {{ $marketingYes }},
                    no: {{ $total - $marketingYes }}
                },
                {
                    id: 'chart-functional',
                    yes: {{ $functionalYes }},
                    no: {{ $total - $functionalYes }}
                },
            ];
            charts.forEach(({
                id,
                yes,
                no
            }) => {
                const ctx = document.getElementById(id);
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [yes, no],
                            backgroundColor: ['#22c55e', '#e5e7eb'],
                            borderWidth: 0,
                            hoverOffset: 2,
                        }]
                    },
                    options: {
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 600
                        },
                    }
                });
            });

            // Applications bar chart – last 6 months
            const appCtx = document.getElementById('chart-applications');
            if (appCtx) {
                new Chart(appCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($monthLabels),
                        datasets: [{
                            data: @json($monthCounts),
                            backgroundColor: '#22c55e',
                            borderRadius: 3,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 9
                                    },
                                    color: '#9ca3af'
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    font: {
                                        size: 9
                                    },
                                    color: '#9ca3af',
                                    precision: 0
                                },
                                beginAtZero: true,
                            },
                        },
                        animation: {
                            duration: 600
                        },
                    }
                });
            }
        });
    </script>
@endsection
