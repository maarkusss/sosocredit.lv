@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Provider & Status Settings' }}</h1>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admincp.statuses.update') }}" method="POST">
            @csrf

            <!-- Providers and Statuses Side by Side -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Providers Base Priority -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Providers Base Priority</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Higher Priority = More Preference
                            </span>
                        </div>
                    </div>

                    @if($providers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($providers as $provider)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-1">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-1">
                                            <input type="hidden" name="providers[{{ $loop->index }}][id]" value="{{ $provider->id }}">
                                            <input type="number"
                                                   name="providers[{{ $loop->index }}][base_priority]"
                                                   value="{{ old('providers.' . $loop->index . '.base_priority', $provider->base_priority) }}"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('providers.' . $loop->index . '.base_priority') border-red-300 @enderror"
                                                   min="1"
                                                   max="100"
                                                   step="1"
                                                   placeholder="1">
                                            @error('providers.' . $loop->index . '.base_priority')
                                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8-1a1 1 0 100 2h1a1 1 0 100-2h-1z" clip-rule="evenodd" />
                            </svg>
                            <p>No providers found.</p>
                        </div>
                    @endif
                </div>

                <!-- Status Multipliers -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Status Multipliers</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Multiplier Affects Final Priority
                            </span>
                        </div>
                    </div>

                    @if($statuses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Multiplier</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($statuses as $status)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-1">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $status->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-1">
                                            <input type="hidden" name="statuses[{{ $loop->index }}][id]" value="{{ $status->id }}">
                                            <input type="number"
                                                   name="statuses[{{ $loop->index }}][multiplier]"
                                                   value="{{ old('statuses.' . $loop->index . '.multiplier', $status->multiplier) }}"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('statuses.' . $loop->index . '.multiplier') border-red-300 @enderror"
                                                   min="0"
                                                   max="10"
                                                   step="0.1"
                                                   placeholder="1.00">
                                            @error('statuses.' . $loop->index . '.multiplier')
                                            <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <p>No statuses found.</p>
                        </div>
                    @endif
                </div>
            </div>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Save All Changes
            </button>
        </form>
    </div>
@endsection
