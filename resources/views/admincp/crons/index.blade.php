@php
/** @var \App\Models\Cron $cron */
@endphp

@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Crons' }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column">Signature</th>
                        <th class="table__column">Description</th>
                        <th class="table__column">Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($crons as $cron)
                        <tr class="table__row">
                            <td class="table__cell">
                                <a href="{{ route('admincp.crons.edit', $cron) }}"
                                   class="link_default">
                                    {{ $cron->signature }}
                                </a>
                            </td>
                            <td class="table__cell">
                                <span class="text-sm">{{ $cron->description }}</span>
                            </td>
                            <td class="table__cell">
                                <label class="flex justify-center">
                                    <input type="hidden"
                                           name="crons[active][{{ $cron->id }}]"
                                           value="0">
                                    <input
                                        class="w-5 h-5 border-gray-300 rounded focus:ring-primary-dark text-primary-normal"
                                        name="crons[active][{{ $cron->id }}]"
                                        value="1"
                                        type="checkbox"
                                        @if ($cron->active) checked @endif>
                                </label>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Crons found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $crons->links('admincp.includes.pagination') }}
            </div>
        </div>
    </div>
@endsection
