@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Redirects' }}</h1>

            @can('add redirects')
                <a class="button_secondary"
                   href="{{ route('admincp.redirects.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add redirect</span>
                </a>
            @endcan
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column">From</th>
                        <th class="table__column">To</th>
                        <th class="table__column">Code</th>
                        <th class="table__column">Hits</th>
                        <th class="table__column">Created</th>
                        @can('delete redirects')
                            <th class="table__column"></th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($redirects as $redirect)
                        <tr class="table__row">
                            <td class="table__cell">
                                <a href="{{ route('admincp.redirects.edit', $redirect->id) }}"
                                   class="link_default">
                                    {{ $redirect->from }}
                                </a>
                            </td>
                            <td class="table__cell">
                                <span>{{ $redirect->to }}</span>
                            </td>
                            <td class="table__cell">
                                <span>{{ $redirect->code }}</span>
                            </td>
                            <td class="table__cell">
                                <span>{{ $redirect->hits }}</span>
                            </td>
                            <td class="table__cell">
                                @if ($redirect->created_at)
                                    <span title="Updated at: {{ $redirect->updated_at->format('d/m/Y H:i') }}">
                                        {{ $redirect->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span><i>Redirect added manually through database!</i></span>
                                @endif
                            </td>
                            @can('delete redirects')
                                <td class="table__cell">
                                    <form action="{{ route('admincp.redirects.destroy', $redirect) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this redirect?');">
                                            <svg class="w-5 h-5 text-red-600 fill-current hover:text-red-700">
                                                <path d="M8.333 15a.833.833 0 0 0 .834-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .833.833zm8.334-10h-3.334v-.833a2.5 2.5 0 0 0-2.5-2.5H9.167a2.5 2.5 0 0 0-2.5 2.5V5H3.333a.833.833 0 0 0 0 1.667h.834v9.166a2.5 2.5 0 0 0 2.5 2.5h6.666a2.5 2.5 0 0 0 2.5-2.5V6.667h.834a.833.833 0 0 0 0-1.667zm-8.334-.833a.833.833 0 0 1 .834-.834h1.666a.833.833 0 0 1 .834.834V5H8.333v-.833zm5.834 11.666a.833.833 0 0 1-.834.834H6.667a.833.833 0 0 1-.834-.834V6.667h8.334v9.166zm-2.5-.833a.833.833 0 0 0 .833-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .834.833z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Redirects found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $redirects->links('admincp.includes.pagination') }}
            </div>
        </div>
    </div>
@endsection
