@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'News' }}</h1>

            @can('add bans')
                <a class="button_secondary"
                   href="{{ route('admincp.news.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add News</span>
                </a>
            @endcan
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <form method="POST" action="{{ route('admincp.news.save') }}">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr class="border-b">
                                <th class="table__column">ID</th>
                                <th class="table__column">Title</th>
                                <th class=table__column">Image</th>
                                <th class=table__column">Language</th>
                                <th class=table__column">Created date</th>
                                <th class=table__column">Updated date</th>
                                <th class="table__column">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($news as $newsItem)
                            <tr class="table__row">
                                <td class="table__cell">
                                    {{ $newsItem->id }}
                                </td>
                                <td class="table__cell">
                                    <a class="underline hover:text-primary-normal"
                                       href="{{ route('admincp.news.edit', $newsItem->id) }}">
                                        {{ $newsItem->title }}
                                    </a>
                                </td>
                                <td class="table__cell text-center">
                                    <img
                                        class="mx-auto max-w-[50px] max-h-[50px]"
                                        src="{{ asset($newsItem->image) }}"
                                        title="{{ asset($newsItem->image) }}"
                                        alt="news image"
                                        loading="lazy"
                                        onerror="this.src='{{ asset('images/no_image.png') }}';"
                                    />
                                </td>
                                <td class="table__cell">
                                    {{ $newsItem->lang }}
                                </td>
                                <td class="table__cell">
                                    {{ $newsItem->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="table__cell">
                                    {{ $newsItem->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="table__cell">
                                    <label class="flex justify-center">
                                        <input type="hidden"
                                               name="news[active][{{ $newsItem->id }}]"
                                               value="0">
                                        <input class="w-5 h-5 border-gray-300 rounded text-primary-normal focus:ring-primary-dark"
                                               name="news[active][{{ $newsItem->id }}]"
                                               type="checkbox"
                                               value="1"
                                               @if ($newsItem->active) checked @endif>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="p-2">
                                        No News found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    @if (!empty($news) && $news->count() > 0)
                        {{ $news->links('admincp.includes.pagination') }}
                    @endif

                    @can('edit news')
                        @if ($news->count() > 0)
                            <div class="button-wrapper">
                                <button type="submit" class="button_submit">
                                    Save
                                </button>
                            </div>
                        @endif
                    @endcan
                </form>
            </div>
        </div>
    </div>
@endsection
