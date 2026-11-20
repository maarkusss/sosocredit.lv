@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Connected URLs' }}</h1>

            <div>
                <a class="flex flex-row items-center px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded hover:bg-gray-800"
                   href="{{ route('admincp.connected-urls.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path
                            d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>
                    <span>Add Connected URL</span>
                </a>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column">URL</th>
                        <th class="table__column">Related URLs</th>
                        <th class="table__column"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($connected_urls as $connected_url)
                        <tr class="table__row">
                            <td class="table__cell">
                                <a href="{{ route('admincp.connected-urls.edit', $connected_url->id) }}"
                                   class="link_default">
                                    {{ config('app.url') . $connected_url->url }}
                                </a>
                            </td>
                            <td class="table__cell">
                                {{ $connected_url->formatted_related_urls }}
                            </td>
                            <td class="table__cell">
                                <form action="{{ route('admincp.connected-urls.destroy', $connected_url) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this connected URL?');">
                                        <svg class="w-5 h-5 text-red-600 fill-current hover:text-red-700">
                                            <path d="M8.333 15a.833.833 0 0 0 .834-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .833.833zm8.334-10h-3.334v-.833a2.5 2.5 0 0 0-2.5-2.5H9.167a2.5 2.5 0 0 0-2.5 2.5V5H3.333a.833.833 0 0 0 0 1.667h.834v9.166a2.5 2.5 0 0 0 2.5 2.5h6.666a2.5 2.5 0 0 0 2.5-2.5V6.667h.834a.833.833 0 0 0 0-1.667zm-8.334-.833a.833.833 0 0 1 .834-.834h1.666a.833.833 0 0 1 .834.834V5H8.333v-.833zm5.834 11.666a.833.833 0 0 1-.834.834H6.667a.833.833 0 0 1-.834-.834V6.667h8.334v9.166zm-2.5-.833a.833.833 0 0 0 .833-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .834.833z"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Connected URLs found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

