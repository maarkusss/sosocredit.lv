@php
    /** @var \App\Models\Section $section */
    /** @var \Ramsey\Collection\Collection $faqs */
@endphp

@extends('layouts.public')

@section('content')
    <main class="flex flex-col max-w-screen-xl md:px-4 py-8 mx-auto">
        <div class="p-4 mb-8 text-base font-normal text-gray-700">
            <h1 class="content__heading">{{ $section->name }}</h1>
            @if($faqs)
                <div class="flex flex-col items-center justify-center gap-2 pt-8 max-w-5xl mx-auto">
                    @foreach($faqs as $faq)
                        @if($faq->active == 0)
                            @continue
                        @endif
                        <div class="flex-col self-stretch px-6 py-4 bg-white rounded-lg shadow-[0px_4px_4px_0px_rgba(0,0,0,0.10)] inline-flex justify-between"
                             x-data="{ open: false }">
                            <button @click="open = !open">
                                <div class="flex items-start justify-between w-full">
                                    <div class="justify-start text-neutral-900 text-base font-medium leading-tight">{{ $faq->question }}</div>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                             x-show="!open">
                                            <path d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z" fill="#081B1A"/>
                                        </svg>
                                        <svg width="22" height="22" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                                             class="pr-1 pt-1"
                                             x-show="open">
                                            <path d="M14 9H2C1.73478 9 1.48043 8.89464 1.29289 8.70711C1.10536 8.51957 1 8.26522 1 8C1 7.73478 1.10536 7.48043 1.29289 7.29289C1.48043 7.10536 1.73478 7 2 7H14C14.2652 7 14.5196 7.10536 14.7071 7.29289C14.8946 7.48043 15 7.73478 15 8C15 8.26522 14.8946 8.51957 14.7071 8.70711C14.5196 8.89464 14.2652 9 14 9Z" fill="#081B1A"/>
                                        </svg>
                                </div>
                            </button>
                            <article class="max-w-full prose mt-4 text-zinc-500 text-sm font-medium leading-tight" id="article_section_text"
                                     x-show="open"
                                     x-transition>
                                {!! $faq->answer !!}
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection
