@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Loan types' }}</h1>

            <div>
                <a class="flex flex-row items-center px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded hover:bg-gray-800"
                   href="{{ route('admincp.loantypes.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path
                                d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>
                    <span>Add loan type</span>
                </a>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <div class="overflow-x-auto whitespace-nowrap">
                    <table class="table">
                        <thead>
                        <tr class="border-b">
                            <th class="table__column">Order</th>
                            <th class="table__column">Name</th>
                            <th class="table__column">Last edited</th>
                            <th class="table__column">Created</th>
                            <th class="table__column">View</th>
                            <th class="table__column">Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($loantypes as $loantype)
                            <tr class="table__row">
                                <td class="table__cell">
                                    <label>
                                        <input
                                                class="input-wrap__input_short"
                                                name="loantypes[order][{{ $loantype->id }}]"
                                                type="text"
                                                value="{{ $loantype->order }}">
                                    </label>
                                </td>
                                <td class="table__cell">
                                    <a class="link_default"
                                       href="{{ route('admincp.loantypes.edit', $loantype->id) }}">
                                        {{ $loantype->name }}
                                    </a>
                                    <br />
                                    <span class="opacity-50" title="URL (route_name)">{{ $loantype->route_name }}</span>
                                </td>
                                <td class="table__cell">
                                    <span>{{ $loantype->updated_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="table__cell">
                                    <span>{{ $loantype->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="table__cell">
                                    @if ($loantype->active)
                                        <a href="{{ route('section', ['name' => $loantype->route_name]) }}"
                                           title="View loantype landing on frontend"
                                           target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                 class="inline w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="table__cell">
                                    <label class="flex justify-center">
                                        <input type="hidden"
                                               name="loantypes[active][{{ $loantype->id }}]"
                                               value="0">
                                        <input
                                                class="w-5 h-5 border-gray-300 rounded text-primary-normal focus:ring-primary-dark"
                                                name="loantypes[active][{{ $loantype->id }}]"
                                                type="checkbox"
                                                value="1"
                                                @if ($loantype->active) checked @endif>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="p-2">
                                        No Loan types found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!empty($loantypes) && $loantypes->count() > 0)
                    <div class="button-wrapper">
                        <button class="button_submit">
                            Save
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

@endsection

