@php
    use App\Models\Document;

    /** @var Document $document */
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <section class="section_slogan px-3 flex justify-center" id="slogan-top" style="height: 1px;"></section>
        <section class="section py-5 sm:py-8">
            <h1 class="content__heading">{{ __('File Download') }}</h1>

            <div class="section__content">
                <div class="flex flex-col items-center justify-center">

                    <div class="landing-form-wrapper">

                        <form action="{{ $document->download_link }}" method="POST" class="w-full">
                            @csrf

                            <input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}"/>

                            <div class="landing-form__steps">
                                <div class="flex flex-col justify-between h-full min-h-[400px]">
                                    <p>
                                        <strong>{{ __('Filename') }}:</strong>
                                        {{ $document->original_filename }}
                                        <br/>
                                        <strong>{{ __('File uploaded at') }}:</strong>
                                        {{ $document->created_at->format('d.m.Y H:i') }}
                                        <br/>
                                        <strong>{{ __('Name') }}:</strong>
                                        {{ $document->name }}
                                        <br/>
                                        <strong>{{ __('Personal code') }}:</strong>
                                        {{ $document->personal_code }}
                                    </p>

                                    <div class="my-4 flex flex-col justify-center items-center text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-24 h-24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        <span class="mt-2">
                                            {{ $document->original_filename }}
                                        </span>
                                    </div>

                                    {{-- Submit button --}}
                                    <button
                                        id="submit_button"
                                        type="submit"
                                        class="landing-form__button text-lg mt-8 disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                                    >
                                        @lang('Download')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
