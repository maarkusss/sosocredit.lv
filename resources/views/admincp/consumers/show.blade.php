@php
/** @var \App\Models\Application $application */
/** @var \App\Models\Consumer\ConsumerSending $sending */
@endphp

@extends('layouts.admin')

@section('content')

    <div class="flex flex-col items-start justify-start w-full min-h-full p-4 bg-transparent">
        <div class="flex flex-wrap justify-between w-full mb-4">
            <h1 class="text-2xl font-medium text-gray-700">Consumer - {{ $consumer->id }}</h1>
        </div>

        <div class="w-full p-4 overflow-x-auto whitespace-nowrap bg-white rounded text-gray-600">
            @foreach($fields as $field)
                <p>
                    <strong>{{ ucwords(str_replace('_', ' ', $field)) }}:</strong>
                    <span class="value">{{ $consumer->{$field} ?: '-' }}</span>
                </p>
            @endforeach
        </div>

        <div class="flex flex-wrap justify-between w-full mt-6 mb-4">
            <h1 class="text-2xl font-medium text-gray-700">Applications ({{ count($applications) }})</h1>
        </div>

        <div class="w-full p-4 overflow-x-auto whitespace-nowrap bg-white rounded text-gray-600">
            <table class="table">
                <thead>
                <tr>
                    <th class="table__column">ID</th>
                    <th class="table__column">Loantype</th>
                    <th class="table__column">Contacts</th>
                    <th class="table__column">Details</th>
                    <th class="table__column">utm_source/campaign</th>
                    <th class="table__column">utm_content/gclid</th>
                    <th class="table__column">Created At</th>
                    <th class="table__column">Exported At</th>
                </tr>
                </thead>
                @if (!empty($applications))
                    @foreach ($applications as $application)
                        <tr class="table__row">
                            <td class="table__cell">
                                {{ $application->id }}
                                @if (!empty($application->automated))
                                    <br />
                                    <span class="badge" title="Automated submission">auto</span>
                                @endif
                            </td>
                            <td class="table__cell">
                                {{ $application->loantype }}
                                <br />
                                <span class="text-sm">{{ $application->amount . currency_symbol() }}, {{ $application->term_months }} mon.</span>
                            </td>
                            <td class="table__cell">
                                {{ $application->phone }}
                                <br />
                                <span class="text-sm">{{ $application->email }}</span>
                            </td>
                            <td class="table__cell text-sm">
                                Salary: {{ $application->salary }}
                                <br />
                                Age: {{ $application->age }}
                                <br />
                                <span title="Credit History">CH:</span>
                                {{ $application->credit_history }}
                            </td>
                            <td class="table__cell text-sm">
                                <span title="utm_source">{{ $application->utm_source }}</span>
                                <br />
                                <span title="utm_campaign">{{ $application->utm_campaign }}</span>
                            </td>
                            <td class="table__cell text-sm">
                                <span title="utm_content">
                                    {{ $application->utm_content }}
                                </span>
                                <br />
                                <span style="font-size: 9px;" title="gclid">
                                    {{ $application->gclid }}
                                </span>
                            </td>
                            <td class="table__cell">
                                {{ $application->created_at->format('d.m.Y H:i') }}

                                <div class="w-[120px] bg-gray-200 rounded-full h-2 mt-1"
                                     title="Completion {{ $application->completion_percentage }}%">
                                    <div
                                        class="@if ($application->completion_percentage > 99) bg-emerald-400 @elseif ($application->completion_percentage > 79) bg-orange-300 @else bg-primary-normal @endif h-2 rounded-full"
                                        style="width: {{ $application->completion_percentage }}%">
                                    </div>
                                </div>
                            </td>
                            <td class="table__cell">
                                {{ $application->last_exported_at ? $application->last_exported_at->format('d.m.Y H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>

        <div class="flex flex-wrap justify-between w-full mt-6 mb-4">
            <h1 class="text-2xl font-medium text-gray-700">Sendings ({{ count($sendings) }})</h1>
        </div>

        <div class="w-full p-4 overflow-x-auto whitespace-nowrap bg-white rounded text-gray-600">
            <table class="table">
                <thead>
                <tr>
                    <th class="table__column">Type</th>
                    <th class="table__column">Subject</th>
                    <th class="table__column">Message</th>
                    <th class="table__column">Created At</th>
                </tr>
                </thead>
                @if (!empty($sendings))
                    @foreach ($sendings as $sending)
                        <tr class="table__row">
                            <td class="table__cell"><span class="badge">{{ $sending->type }}</span></td>
                            <td class="table__cell">{{ $sending->subject }}</td>
                            <td class="table__cell">{{ $sending->message }}</td>
                            <td class="table__cell">{{ $sending->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>

        <div class="flex flex-wrap justify-between w-full mt-6 mb-4">
            <h1 class="text-2xl font-medium text-gray-700">Commissions ({{ $consumer->commissions->count() }})</h1>
        </div>

        <div class="w-full p-4 overflow-x-auto whitespace-nowrap bg-white rounded text-gray-600">
            <table class="table">
                <thead>
                <tr>
                    <th class="table__column">Lender</th>
                    <th class="table__column">Status</th>
                    <th class="table__column">utm_source</th>
                    <th class="table__column">s7</th>
                    <th class="table__column">Commission Date</th>
                </tr>
                </thead>
                @if ($consumer->commissions->count() > 0)
                    <tbody>
                        @foreach ($consumer->commissions as $commission)
                            <tr class="table__row">
                                <td class="table__cell">{{ $commission->lender ? $commission->lender->name : '-' }}</td>
                                <td class="table__cell"><span class="badge @if ($commission->status === 'A') badge-success @endif">{{ $commission->status }}</span></td>
                                <td class="table__cell">{{ $commission->utm_source }}</td>
                                <td class="table__cell">{{ $commission->s7 }}</td>
                                <td class="table__cell">{{ $commission->commission_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>
        </div>

        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

    </div>

@endsection
