@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'FAQs' }}</h1>

            @can('add bans')
                <a class="button_secondary"
                   href="{{ route('admincp.faqs.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add FAQ</span>
                </a>
            @endcan
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <form method="POST" action="{{ route('admincp.faqs.save') }}">
                    @csrf
                    <table class="table">
                        <thead>
                            <tr class="border-b">
                                <th class="table__column">Order</th>
                                <th class="table__column">Question</th>
                                <th class=table__column">Answer</th>
                                <th class="table__column">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($faqs as $faq)
                            <tr class="table__row">
                                <td class="table__cell">
                                    <label>
                                        <input
                                            class="w-16 px-2 py-1 text-center border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                                            name="faqs[order][{{ $faq->id }}]" type="text"
                                            value="{{ $faq->order }}">
                                    </label>
                                </td>
                                <td class="table__cell">
                                    <a class="underline hover:text-primary-normal"
                                       href="{{ route('admincp.faqs.edit', $faq->id) }}">
                                        {{ $faq->question }}
                                    </a>
                                </td>
                                <td class="table__cell">
                                    {{ mb_substr(strip_tags($faq->answer), 0, 100) }}..
                                </td>
                                <td class="table__cell">
                                    <label class="flex justify-center">
                                        <input type="hidden"
                                               name="faqs[active][{{ $faq->id }}]"
                                               value="0">
                                        <input class="w-5 h-5 border-gray-300 rounded text-primary-normal focus:ring-primary-dark"
                                               name="faqs[active][{{ $faq->id }}]"
                                               type="checkbox"
                                               value="1"
                                               @if ($faq->active) checked @endif>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="p-2">
                                        No FAQs found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    @if (!empty($faqs) && $faqs->count() > 0)
                        {{ $faqs->links('admincp.includes.pagination') }}
                    @endif

                    @can('edit faqs')
                        @if ($faqs->count() > 0)
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
