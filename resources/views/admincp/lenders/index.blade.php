@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Lenders' }}</h1>

            <div class="flex flex-col items-start sm:flex-row sm:items-center">
                <a href="{{ route('admincp.lenders.campaigns.index') }}"
                   class="button_secondary mr-2">
                    <span>View Campaigns</span>
                </a>
                <form>
                    <select class="px-3 py-2 pr-8 mt-2 text-gray-700 bg-white border border-gray-300 rounded sm:mt-0 sm:mr-2 focus:border-primary-dark focus:ring-primary-dark"
                            name="sorting"
                            onchange="this.form.submit()">
                        <option value="">Select a layout</option>
                        @foreach ($sortings as $sorting)
                            <option value="{{ $sorting->id }}"
                                    @if ($sorting->id == request()->sorting) selected @endif>{{ $sorting->campaign_name }}
                                ({{ $sorting->clicks }})
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admincp.lenders.add.index') }}"
                   class="button_secondary">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>
                    <span>Add lender</span>
                </a>
            </div>
        </div>

        <form method="POST"
              class="w-full p-4 bg-white rounded">
            @csrf
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column" width="50" style="font-size: 11px;">Campaign ID</th>
                        <th class="table__column">Lender</th>
                        <th class="table__column">Position</th>
                        <th class="table__column">Frequency</th>
                        <th class="table__column">Daily EPC</th>
                        <th class="table__column">Daily EPC before</th>
                        <th class="table__column">Guaranteed EPC</th>
                        <th class="table__column">Earnings</th>
                        <th class="table__column">Clicks</th>
                        <th class="table__column">EPC</th>
                        <th class="table__column">Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lenders as $lender)
                        <tr>
                            <td class="p-3 text-center border">
                                <span class="badge @if (empty($lender->campaign_id)) badge-danger @else badge-secondary @endif"
                                    @if (empty($lender->campaign_id)) title="Campaign_id is empty. Will not be able to create Lender Filter." @endif
                                >
                                    {{ $lender->campaign_id }}
                                </span>
                            </td>
                            <td class="p-3 text-center border">
                                <a href="{{ route('admincp.lenders.edit.index', ['lender_id' => $lender->id]) }}">
                                    @if (!empty($lender->image))
                                        <img class="object-contain h-16 mx-auto"
                                             src="{{ $lender->image }}"
                                             alt="{{ $lender->name }}">
                                    @else
                                        <svg class="w-5 mx-auto text-red-400" viewBox="0 0 512 512">
                                            <path fill="currentColor"
                                                  d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"></path>
                                        </svg>
                                    @endif
                                    <span>{{ $lender->name }}</span>
                                </a>
                            </td>
                            <td class="p-3 text-center border">
                                <label>
                                    <input class="input-wrap__input_short"
                                           name="position[{{ $lender->id }}]"
                                           type="text"
                                           value="{{ $lender->position }}">
                                </label>
                            </td>
                            <td class="p-3 text-center border">
                                <label>
                                    <input class="input-wrap__input_short"
                                           name="frequency[{{ $lender->id }}]"
                                           type="number"
                                           value="{{ $lender->frequency }}">
                                </label>
                            </td>
                            <td class="p-3 text-center border">
                                <label>
                                    <input class="input-wrap__input_short"
                                           name="daily_epc[{{ $lender->id }}]"
                                           type="number"
                                           value="{{ $lender->daily_epc }}">
                                </label>
                            </td>
                            <td class="p-3 text-center border">
                                <label>
                                    <input class="input-wrap__input_short"
                                           name="daily_epc_before[{{ $lender->id }}]"
                                           type="number"
                                           value="{{ $lender->daily_epc_before }}">
                                </label>
                            </td>
                            <td class="p-3 text-center border">
                                <label>
                                    <input class="input-wrap__input_short"
                                           name="guaranteed_epc[{{ $lender->id }}]"
                                           type="text"
                                           value="{{ $lender->guaranteed_epc }}">
                                </label>
                            </td>
                            <td class="p-3 text-center border">
                                <span>{{ $lender->earnings }}</span>
                            </td>
                            <td class="p-3 text-center border">
                                <span>{{ $lender->clicks }}</span>
                            </td>
                            <td class="p-3 text-center border">
                                <span>{{ $lender->epc }}</span>
                            </td>
                            <td class="p-3 text-center border">
                                <label class="flex justify-center">
                                    @if ($lender->is_provider)
                                        <svg width="32" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                            <title>Provider</title>
                                            <path d="M37 22L34 25L23 14L26 11C27.5 9.5 33 7 37 11C41 15 38.5 20.5 37 22Z"
                                                  fill="#3faf4f" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11 26L14 23L25 34L22 37C20.5 38.5 15 41 11 37C7 33 9.5 27.5 11 26Z"
                                                  fill="#3faf4f" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M42 6L37 11" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M23 32L27 28" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 42L11 37" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16 25L20 21" stroke="#000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @else
                                        <input type="hidden"
                                               name="active[{{ $lender->id }}]"
                                               value="0">
                                        <input class="w-5 h-5 border-gray-300 rounded text-primary-normal focus:ring-primary-dark"
                                               name="active[{{ $lender->id }}]"
                                               type="checkbox"
                                               value="1"
                                               @if ($lender->active) checked @endif>
                                    @endif
                                </label>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Lenders found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if (!request()->sorting && !empty($lenders))
                <div class="flex justify-end mt-6">
                    <button class="button_submit"
                            type="submit"
                            name="action"
                            value="save">
                        Save
                    </button>
                </div>
            @endif
        </form>

@endsection
