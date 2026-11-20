@extends('layouts.admin')

@section('content')
    @php
        $groups = [
            'Personal' => [
                'first_name',
                'last_name',
                'personal_code',
                'age',
                'email',
                'phone',
                'phone_verified_at',
                'employment_status',
                'education',
                'marital_status',
                'is_political_person',
                'dependants',
            ],
            'Loan' => [
                'amount',
                'term_months',
                'loantype',
                'credit_history',
                'salary',
                'monthly_debit',
                'disbursement_iban',
                'has_real_property',
                'has_bank_attachment',
            ],
            'Address' => ['address_street', 'address_city', 'address_zipcode'],
            'Business' => ['company_name', 'company_reg_nr', 'company_contact_person', 'cadastral_number'],
            'Tracking' => [
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'gclid',
                'goodaff_click_id',
                'referrer',
                'ip',
                'user_agent',
                'fingerprint',
            ],
            'Meta' => [
                'token',
                'webmaster_token',
                'consumer_id',
                'coupon_code',
                'agree_to_terms',
                'subscribed',
                'completion_percentage',
                'will_send_docs_via_email',
                'first_offer_hash',
                'second_offer_hash',
                'pdf',
                'pdf_downloaded_from_ip',
                'pdf_downloaded_at',
                'last_exported_at',
                'created_at',
                'updated_at',
            ],
        ];

        $statusColors = [
            'Approved' => 'bg-green-100 text-green-700',
            'Done' => 'bg-green-200 text-green-800',
            'Expired' => 'bg-yellow-100 text-yellow-700',
            'Reviewing' => 'bg-blue-100 text-blue-700',
            'Rejected' => 'bg-red-50 text-red-500',
            'Cannot' => 'bg-gray-100 text-gray-400',
        ];
    @endphp

    <div class="flex flex-col items-start justify-start w-full min-h-full p-4 bg-transparent">
        <div class="flex flex-wrap justify-between w-full mb-4">
            <h1 class="text-2xl font-medium text-gray-700">Application - {{ $application->id }}</h1>
        </div>

        <div
            class="grid items-start w-full grid-cols-6 gap-2 p-4 overflow-x-auto bg-white rounded whitespace-nowrap text-gray-60">

            {{-- Lender responses --}}
            @if (!empty($application->exports_summary))
                <div class="col-span-6 overflow-hidden border border-gray-200 rounded">
                    <div class="px-2 py-0.5 border-b border-gray-100">
                        <span class="text-xs font-bold">Lender responses</span>
                    </div>
                    @php
                        $statusOrder = [
                            'Approved' => 0,
                            'Done' => 1,
                            'Reviewing' => 2,
                            'Expired' => 3,
                            'Rejected' => 4,
                            'Cannot' => 5,
                        ];
                        $sorted = collect($application->exports_summary)
                            ->sortBy(fn($status) => $statusOrder[$status] ?? 99)
                            ->all();
                    @endphp
                    <div class="flex flex-wrap gap-1.5 p-2">
                        @foreach ($sorted as $lender => $status)
                            @php $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-400'; @endphp
                            <div class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs {{ $color }}">
                                <span class="font-medium capitalize">{{ $lender }}</span>
                                <span class="opacity-60">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Field groups --}}
            @foreach ($groups as $groupName => $groupFields)
                <div
                    class="overflow-hidden border border-gray-200 rounded @if ($groupName === 'Meta' || $groupName === 'Personal' || $groupName === 'Tracking') col-span-2 @endif">
                    <div class="px-2 py-0.5 border-b border-gray-100">
                        <span class="text-xs font-bold">{{ $groupName }}</span>
                    </div>
                    <table class="w-full">
                        @foreach ($groupFields as $field)
                            @php $value = $application->{$field} @endphp
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="px-2 py-0.5 text-xs whitespace-nowrap w-2/5">{{ str_replace('_', ' ', $field) }}
                                </td>
                                <td class="px-2 py-0.5 text-xs font-mono truncate"
                                    title="@if (!($value instanceof \Carbon\Carbon) && !is_array($value) && !is_object($value)) {{ $value }} @endif">
                                    @if (is_null($value) || $value === '')
                                        <span class="text-gray-400">X</span>
                                    @elseif ($value instanceof \Carbon\Carbon)
                                        {{ $value->format('Y-m-d H:i:s') }}
                                    @elseif (is_bool($value) ||
                                            (is_int($value) && in_array($value, [0, 1])) ||
                                            (is_string($value) && in_array($value, ['0', '1'])))
                                        @if ($value)
                                            <span class="text-green-500">Yes</span>
                                        @else
                                            <span class="text-red-500">No</span>
                                        @endif
                                    @elseif (is_array($value) || is_object($value))
                                        {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach

        </div>
    </div>
@endsection
