@php
    use App\Models\Section;

    $default_loantype_section = Section::query()
        ->where('template', Section::TEMPLATE_APPLICANT_FORM_LANDING_PAGE)
        ->where('lang', app()->getLocale())
        ->orderBy('order', 'ASC')
        ->select(['id', 'name', 'route_name'])
        ->first();

    $form_action = '/' . app()->getLocale() . '/' . $default_loantype_section->route_name;
@endphp

@extends('layouts.public', ['meta_robots' => 'noindex, nofollow'])

@section('content')
    <main>
        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="section_narrower md:section py-5 sm:py-8 min-h-[600px]">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-col flex-shrink-0 w-full max-w-screen-md min-h-96" x-data="{ open: false }">
                        <div class="flex flex-col items-start justify-between my-2 sm:flex-row">
                            <h1 class="mb-2 ml-2 text-lg font-bold sm:ml-0 sm:mb-0 md:text-xl text_neutral-900">
                                {{ __('My applications') }}</h1>

                            <button
                                class="flex flex-row items-center justify-center w-full gap-2 px-2 py-2 text-base font-medium text-center border rounded md:w-auto md:justify-start md:px-3 text_primary border_primary hover:bg-gray-200"
                                @click="open = !open">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z"
                                        fill="#3FAF4F" />
                                </svg>

                                @lang('New application')
                            </button>
                        </div>
                        <div class="" x-show="open" x-data="{
                            selectedUrl: @js($default_form_type['url'] ?? null),
                            selectedSectionId: @js($default_form_type['id'] ?? null),
                            selectedLoanType: @js($default_form_type['name'] ?? null)
                        }">
                            <form :action="selectedUrl || '{{ $form_action }}'" method="POST" class="w-full"
                                id="applicant_form">
                                @csrf
                                <input type="hidden" name="section_id" :value="selectedSectionId">
                                <input type="hidden" name="loantype" :value="selectedLoanType">
                                <div class="mt-1 mb-8">
                                    @include('includes.applicant-form.app-selector')
                                </div>
                            </form>
                        </div>

                        <div class="w-full p-3 bg-white border rounded-lg shadow-xl md:p-6 border-zinc-200 lg:p-8">
                            @if ($applications->count() > 0)
                                @php
                                    $counter = 0;
                                @endphp
                                <div class="overflow-x-auto whitespace-nowrap">
                                    @foreach ($applications as $application)
                                        <div
                                            class="flex flex-col md:flex-row mb-2 md:mb-4 py-4 md:py-5 px-5 justify-between bg-[#EAF6ED] rounded-lg md:items-center">
                                            <div class="mb-2 md:mb-4">
                                                <div>
                                                    <h4 class="text-lg font-bold leading-normal text-neutral-900">
                                                        {{ $application->section->name ?? '' }}
                                                        {{ $application->amount . currency_symbol() }}</h4>
                                                    <p class="text-sm font-medium leading-tight text-neutral-900">
                                                        {{ $application->created_at->format('d.m.Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-4 mb-2 md:mb-0">
                                                @if ($application->completion_percentage >= 80)
                                                    <a href="{{ route('consumers.applications.show', $application->token) }}"
                                                        class="block w-full px-4 py-2 mx-auto text-base font-medium text-center text-white border border-green-600 rounded md:px-6 button_primary">
                                                        {{ __('View') }}
                                                    </a>
                                                @endif
                                                @if ($application->completion_percentage < 80)
                                                    <a href="{{ route('applications.continue', $application->token) }}"
                                                        class="block w-full px-4 py-2 mx-auto text-base font-medium text-center text-white border border-green-600 rounded md:px-6 button_primary">
                                                        {{ __('Continue') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-lg text_neutral-900">{{ __('You have no applications yet') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
