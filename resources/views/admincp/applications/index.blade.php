@php
    use App\Models\Application;

    /** @var Application $application */

@endphp

@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Applications' }}</h1>
            <div>
                <form>
                    @include('admincp.includes.input', [
                        'id' => 'search',
                        'name' => 'search',
                        'label' => '',
                        'placeholder' => 'Search by phone or email',
                        'value' => request()->input('search'),
                        'class' => 'input-wrap__input_light',
                    ])
                </form>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                        <tr class="border-b">
                            <th class="table__column">ID</th>
                            <th class="table__column">Phone / Email</th>
                            <th class="table__column">Subscribed</th>
                            <th class="table__column">Loan</th>
                            <th class="table__column">PDF</th>
                            <th class="table__column">Will Send</th>
                            <th class="table__column">GoCardless</th>
                            <th class="table__column">Lang</th>
                            <th class="table__column">utm</th>
                            <th class="table__column">Created</th>
                            <th class="table__column">Updated</th>
                            <th class="table__column">Exported</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr class="table__row">
                                <td class="table__cell">
                                    <a href="{{ route('admincp.applications.applications.show', ['application' => $application]) }}"
                                        class="hover:underline">
                                        {{ $application->id }}
                                    </a>
                                    @if (!empty($application->automated))
                                        <br />
                                        <span class="badge" title="Automated submission">auto</span>
                                    @endif
                                </td>
                                <td class="table__cell">
                                    {{ $application->phone }}
                                    <br />
                                    <span class="text-sm">{{ $application->email }}</span>
                                </td>
                                <td class="table__cell">
                                    @if ($application->subscribed)
                                        <svg class="w-5 h-5 mx-auto text-green-500 fill-current">--}}
                                            <path
                                                d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z" />
                                        </svg>
                                    @endif
                                </td>
                                <td class="table__cell">
                                    @if ($application->loantype)
                                        <span class="badge badge-secondary">{{ $application->loantype }}</span>
                                        <br />
                                    @endif

                                    {{ $application->amount . currency_symbol() }}

                                    @if (!empty($application->term_months))
                                        , {{ $application->term_months }} months
                                    @endif
                                </td>
                                <td class="table__cell">
                                    @if ($application->pdf)
                                        <a href="{{ route('admincp.applications.view-pdf', $application->token) }}"
                                            target="_blank" title="Skatīt PDF">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="table__cell">
                                    @if ($application->will_send_docs_via_email)
                                        <svg class="w-5 h-5 mx-auto text-green-500 fill-current">--}}
                                            <path
                                                d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z" />
                                        </svg>
                                    @endif
                                </td>
                                <td class="table__cell">
                                    {{ $application->gocardless_sessions_count }}
                                </td>
                                <td class="table__cell">
                                    @if (!empty($application->lang))
                                        <img class="inline ml-2" width="20" height="15"
                                            src="https://flagicons.lipis.dev/flags/4x3/{{ flag_filename($application->lang) }}.svg" />
                                    @endif
                                </td>
                                <td class="leading-none table__cell">
                                    <span class="text-sm" title="utm_source">
                                        {{ $application->utm_source }}
                                    </span>
                                    @if (!empty($application->utm_campaign))
                                        <br /><span class="text-sm"
                                            title="utm_campaign">{{ $application->utm_campaign }}</span>
                                    @endif
                                    @if (!empty($application->utm_content))
                                        <br /><span class="text-xs"
                                            title="utm_content">{{ $application->utm_content }}</span>
                                    @endif
                                </td>
                                <td class="table__cell" width="120">
                                    {{ $application->created_at ? $application->created_at->format('d.m.Y H:i') : '-' }}
                                    <div class="w-[120px] bg-gray-200 rounded-full h-2 mt-1"
                                        title="Completion {{ $application->completion_percentage }}%">
                                        <div class="@if ($application->completion_percentage > 99) bg-emerald-400 @elseif ($application->completion_percentage > 79) bg-orange-300 @elseif ($application->completion_percentage > 39) bg-yellow-300 @else bg-red-600 @endif h-2 rounded-full"
                                            style="width: {{ $application->completion_percentage }}%">
                                        </div>
                                    </div>
                                </td>
                                <td class="table__cell">
                                    {{ $application->updated_at ? $application->updated_at->format('d.m.Y H:i') : '-' }}
                                </td>
                                <td class="table__cell" width="120">
                                    {{ $application->last_exported_at ? $application->last_exported_at->format('d.m.Y H:i') : '-' }}

                                    <form action="{{ route('admincp.applications.export-to-data', $application) }}"
                                        method="POST">
                                        @csrf

                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to submit this application to Data?')"
                                            class="mx-auto text-sm button button_secondary">
                                            Export
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="p-2">
                                        No Applications found
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $applications->links('admincp.includes.pagination') }}
        </div>
    </div>
@endsection
