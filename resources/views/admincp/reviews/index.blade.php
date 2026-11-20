@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Reviews' }}</h1>

            @can('add bans')
                <a class="button_secondary"
                   href="{{ route('admincp.reviews.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add Review</span>
                </a>
            @endcan
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                        <tr class="border-b">
                            <th class="table__column text-left">Section</th>
                            <th class="table__column text-left">Image</th>
                            <th class="table__column text-left">Name</th>
                            <th class="table__column text-left">Text</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($reviews as $review)
                        <tr class="table__row">
                            <td class="table__cell text-left">
                                @if ($review->section)
                                    {{ $review->section->name }}
                                @endif
                            </td>
                            <td class="table__cell text-left">
                                <a class="underline hover:text-primary-normal"
                                   href="{{ route('admincp.reviews.edit', $review->id) }}">
                                    <img src="{{ $review->image }}"
                                     class="w-8 h-8"
                                     alt="" />
                                </a>
                            </td>
                            <td class="table__cell text-left">
                                <a class="underline hover:text-primary-normal"
                                   href="{{ route('admincp.reviews.edit', $review->id) }}">
                                    {{ $review->name }}
                                </a>
                            </td>
                            <td class="table__cell text-left">{{ mb_substr(strip_tags($review->text), 0, 50) }}..</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Reviews found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                @if (!empty($reviews) && $reviews->count() > 0)
                    {{ $reviews->links('admincp.includes.pagination') }}
                @endif
            </div>
        </div>
    </div>
@endsection
