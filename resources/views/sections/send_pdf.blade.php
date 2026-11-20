@php
    use App\Models\Section;

    /** @var Section $section */

    $document_sent_successfully = false;
    if (session()->has('document_sent_successfully')) {
        $document_sent_successfully = true;
        session()->forget('document_sent_successfully');
    }
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <section class="section_slogan px-3 flex justify-center" id="slogan-top" style="height: 1px;"></section>
        <section class="section py-5 sm:py-8">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">

                @if (!empty($section->h1_description))
                        <div class="section__content__hero">

                            <div class="mt-4 w-full text-base leading-5 text-center">
                                {{ $section->h1_description }}
                            </div>
                        </div>
                    @endif

                    <div class="landing-form-wrapper applicant-form">

                        @if ($document_sent_successfully)

                            <div class="mt-10 text-center max-w-sm mx-auto min-h-[600px]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="#30d090"
                                     class="w-10 h-10 mx-auto mb-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                <p class="font-bold text-lg">
                                    @lang('applicant_form.document_sent_successfully')
                                </p>

                                <p class="mt-4 text-xs font-normal leading-tight text-center text-gray-600">
                                    * @lang('applicant_form.during_working_hours')
                                </p>
                            </div>

                        @else

                            <form action="{{ route('send-pdf') }}" method="POST" class="w-full" id="applicant_form" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}"/>

                                <div class="landing-form__steps">
                                    @include('admincp.includes.infobox')

                                    <div class="flex flex-col justify-between h-full min-h-[400px]">
                                        <div>
                                            <h2 class="text-base text-gray-500 border-b border-gray-300 pb-4 mb-4">@lang('applicant_form.bank_note_in_pdf')</h2>

                                            @if (!empty($section->text))
                                                <p>
                                                    {!! $section->text !!}
                                                </p>
                                            @endif

                                            <div class="mt-4 applicant-form__input-wrap">
                                                <label for="name">@lang('First name'), @lang('Last name') <sup>*</sup></label>
                                                <input class="applicant-form__input"
                                                       type="text"
                                                       name="name"
                                                       id="name"
                                                       value="{{ old('name') }}"
                                                       required
                                                />
                                            </div>

                                            <div class="mt-4 applicant-form__input-wrap">
                                                <label for="personal_code">@lang('Personal code')<sup>*</sup></label>
                                                <input class="applicant-form__input"
                                                       type="text"
                                                       name="personal_code"
                                                       id="personal_code"
                                                       value="{{ old('personal_code') }}"
                                                       required
                                                />
                                            </div>

                                            <div
                                                class="mt-4 mb-4 rounded border-2 border-gray-400 p-4 text-center cursor-pointer hover:border-[#ff7473] hover:text-gray-500"
                                            >
                                                <div class="mx-6">
                                                    <div onclick="openFileBrowser()"
                                                         class="flex flex-row justify-center items-center disabled:opacity-80 disabled:cursor-not-allowed"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                             class="w-8 h-8 mr-3">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>
                                                        </svg>

                                                        <div>@lang('Attach file')</div>
                                                    </div>

                                                    <input type="file"
                                                           name="pdf"
                                                           accept="application/pdf"
                                                           id="attachPdfFileHandler"
                                                           class="hidden"
                                                    />

                                                    <p class="font-semibold"
                                                       id="attachPdfFileName"></p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Submit button --}}
                                        <button
                                            id="submit_button"
                                            type="submit"
                                            class="landing-form__button text-lg mt-8 disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                                        >
                                            @lang('Send')

                                            @include('includes.loading-spinner')
                                        </button>
                                    </div>
                                </div>
                            </form>

                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
            defer></script>

    <script>
        const applicantForm = document.getElementById('applicant_form'),
            submitButton = document.getElementById("submit_button"),
            loadingSpinner = applicantForm.querySelector(".loading-spinner");

        if (applicantForm && submitButton) {
            applicantForm.addEventListener('submit', (e) => {
                if (submitButton) {
                    submitButton.disabled = true;
                }

                if (loadingSpinner) {
                    loadingSpinner.classList.remove("hidden");
                }

                return true;
            });
        }
    </script>


    <script>
        function openFileBrowser() {
            const file_handler = document.getElementById("attachPdfFileHandler");
            file_handler.click();
        }

        function submitPdfFile(e) {
            const filename_container = document.getElementById("attachPdfFileName");
            if (e && e.name) {
                filename_container.innerText = e.name;
            }
        }

        let pdf_upload_input = document.getElementById("attachPdfFileHandler");
        if (pdf_upload_input) {
            pdf_upload_input.onchange = function () {
                const input = this.files[0],
                    filename_container = document.getElementById("attachPdfFileName");
                if (input && input.name) {
                    filename_container.innerText = input.name;
                }
            };
        }
    </script>
@endsection
