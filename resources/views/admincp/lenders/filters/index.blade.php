@php
    use App\Models\Lender;
@endphp

@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Lender Filters' }}</h1>

            @can('add lenders')
                <a class="button_secondary"
                   href="{{ route('admincp.lenders.filters.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path
                            d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add lender filter</span>
                </a>
            @endcan
        </div>

        <p class="mt-1 mb-4">
            Lender Filters allow excluding specific lenders from Lender listings for specific
            <code>utm_source</code> and <code>utm_campaign</code> values. As well as assigning a random
            <code>token</code> value to identify specific consumer.
        </p>

        <div class="content-wrapper__form-wrapper">
            <form action="?" method="GET">
                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'utm_source',
                       'name' => 'utm_source',
                       'label' => 'UTM Source',
                       'value' => old('utm_source', request()->get('utm_source') ?? ''),
                   ])

                    @include('admincp.includes.input', [
                      'id' => 'utm_campaign',
                      'name' => 'utm_campaign',
                      'label' => 'UTM Campaign',
                      'value' => old('utm_campaign', request()->get('utm_campaign') ?? ''),
                  ])

                    @include('admincp.includes.input', [
                       'id' => 'token',
                       'name' => 'token',
                       'label' => 'Token',
                       'value' => old('token', request()->get('token') ?? ''),
                   ])

                    <div class="flex flex-col justify-end pb-1">
                        <button type="submit" class="button_secondary w-fit">
                            Search
                        </button>
                    </div>
                </div>

                <div>
                    <p><strong>Filters found:</strong> {{ $filters->total() }}</p>
                </div>
            </form>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column text-left">utm_source *</th>
                        <th class="table__column text-left">utm_campaign</th>
                        <th class="table__column text-left">token</th>
                        <th class="table__column text-left" style="max-width: 140px;">Exclusion List</th>
                        <th class="table__column text-left" style="max-width: 140px;">Frequencies</th>
                        <th class="table__column">Source</th>
                        <th class="table__column">Hits</th>
                        <th class="table__column">View</th>
                        <th class="table__column">Active</th>
                        <th class="table__column">Edit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($filters as $filter)
                        <tr>
                            <td class="table__cell text-left border">
                                {{ $filter->utm_source }}
                            </td>
                            <td class="table__cell text-left border">
                                {{ $filter->utm_campaign }}
                            </td>
                            <td class="table__cell text-left border">
                                {{ $filter->token }}
                            </td>
                            <td class="table__cell text-left border text-sm align-top">
                                @if (!empty($filter->rules) && !empty($filter->rules['exclude_campaigns']))
                                    <ul class="list-unstyled">
                                        @foreach (Lender::query()
                                            ->whereIn('campaign_id', (array) $filter->rules['exclude_campaigns'])
                                            ->pluck('name')
                                            ->map(fn($name) => ' ❌ ' . $name)
                                            ->sort()
                                            ->toArray() as $lender_name)

                                            <li>{{ $lender_name }}</li>

                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td class="table__cell text-left border text-sm align-top">
                                @if (!empty($filter->rules) && !empty($filter->rules['set_campaigns_frequencies']))
                                    <ul class="list-unstyled">
                                        @foreach (Lender::query()
                                            ->whereIn('campaign_id', array_keys((array) $filter->rules['set_campaigns_frequencies']))
                                            ->pluck('name', 'campaign_id')
                                            ->map(fn($name, $campaign_id) => ' 🔁 ' . $name . ': ' . ($filter->rules['set_campaigns_frequencies'])->{$campaign_id})
                                            ->sort()
                                            ->toArray() as $lender_name)

                                            <li>{{ $lender_name }}</li>

                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td class="table__cell border">
                                @if (!empty($filter->source))
                                    <span class="badge badge-secondary">
                                        {{ $filter->source }}
                                    </span>
                                @endif
                            </td>
                            <td class="table__cell border">
                                {{ $filter->hits }}
                            </td>
                            <td class="table__cell border">
                                @if (!empty($filter->url) && $filter->active)
                                    <a href="{{ $filter->url }}"
                                       title="View offer page on frontend"
                                       target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor"
                                             class="inline w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                            <td class="table__cell border">
                                @if ($filter->active)
                                    <svg class="w-5 h-5 mx-auto text-green-500 fill-current">--}}
                                        <path
                                            d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z"/>
                                    </svg>
                                @endif
                            </td>
                            <td class="table__cell border">
                                <a href="{{ route('admincp.lenders.filters.edit', $filter->id) }}"
                                   class="link_default">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Lender Filters found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $filters->links('admincp.includes.pagination') }}
        </div>
    </div>

@endsection
