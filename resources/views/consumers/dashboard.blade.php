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
        <section class="py-6 sm:py-10 min-h-[700px] bg-slate-50">
            <div class="max-w-[1136px] px-4 mx-auto w-full" x-data="{ open: false }">

                {{-- Page Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-900 tracking-tight">
                            @if (!empty($consumer->first_name))
                                @lang('Sveicināti'), {{ $consumer->first_name }}!
                            @else
                                @lang('Mans profils')
                            @endif
                        </h1>
                        <p class="text-sm sm:text-base text-gray-500 mt-1">
                            @lang('Pārvaldiet savu profila informāciju, paziņojumu iestatījumus un kredītu pieteikumus')
                        </p>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm sm:text-base font-semibold text-white bg-[#3FAF4F] hover:bg-green-700 rounded-lg shadow-sm transition-all duration-150 cursor-pointer self-start sm:self-auto"
                        @click="open = !open">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>@lang('Jauns pieteikums')</span>
                    </button>
                </div>

                {{-- New Application Collapse Panel --}}
                <div class="mb-8" x-show="open" x-transition x-cloak x-data="{
                    selectedUrl: @js($default_form_type['url'] ?? null),
                    selectedSectionId: @js($default_form_type['id'] ?? null),
                    selectedLoanType: @js($default_form_type['name'] ?? null)
                }">
                    <div class="p-6 bg-white border border-green-200 rounded-xl shadow-md">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">@lang('Izvēlieties aizdevuma veidu un summu')</h3>
                        <form :action="selectedUrl || '{{ $form_action }}'" method="POST" class="w-full" id="applicant_form">
                            @csrf
                            <input type="hidden" name="section_id" :value="selectedSectionId">
                            <input type="hidden" name="loantype" :value="selectedLoanType">
                            @include('includes.applicant-form.app-selector')
                        </form>
                    </div>
                </div>

                {{-- Profile & Notification Settings Card --}}
                <div class="w-full mb-8 bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-[#3FAF4F]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">@lang('Profila informācija un paziņojumu iestatījumi')</h2>
                            <p class="text-xs sm:text-sm text-gray-500">@lang('Šeit varat atjaunināt savu tālruņa numuru un izvēlēties saņemt jaunumus')</p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="flex items-center gap-3 p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('consumers.profile.phone') }}" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    @lang('E-pasts')
                                </label>
                                <div class="relative">
                                    <input type="email" value="{{ $consumer->email ?? '' }}"
                                        class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-lg text-gray-700 cursor-not-allowed select-none font-medium"
                                        readonly disabled />
                                    <span class="absolute right-3 top-3 text-xs text-green-600 font-semibold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @lang('Verificēts')
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">@lang('E-pasts tiek izmantots autorizācijai sistēmā')</p>
                            </div>

                            <div>
                                <label for="profile_phone" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    @lang('Tālruņa numurs')
                                </label>
                                <input type="text" name="phone" id="profile_phone"
                                    value="{{ old('phone', $consumer->phone ? ($consumer->phone) : '') }}"
                                    placeholder="+371 20000000"
                                    class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" />
                                <p class="text-xs text-gray-400 mt-1">@lang('Nepieciešams, lai saņemtu apstiprinājumu par aizdevēja lēmumu')</p>
                            </div>
                        </div>

                        {{-- Marketing Notifications Toggles --}}
                        <div class="pt-6 border-t border-gray-100">
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">
                                @lang('Jaunumu un aizdevēju piedāvājumu saņemšana')
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50/30 transition select-none">
                                    <input type="hidden" name="subscribed_email" value="0">
                                    <input type="checkbox" name="subscribed_email" value="1"
                                        class="mt-1 w-5 h-5 text-[#3FAF4F] border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                        @checked(!empty($consumer->subscribed_email) || (!empty($consumer->subscribed) && !isset($consumer->subscribed_email)))>
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800">@lang('Saņemt jaunumus un piedāvājumus e-pastā')</span>
                                        <span class="block text-xs text-gray-500 mt-0.5">@lang('Svarīgi jaunumi par zemākām kredītu likmēm un akcijām uz Jūsu e-pastu')</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50/30 transition select-none">
                                    <input type="hidden" name="subscribed_sms" value="0">
                                    <input type="checkbox" name="subscribed_sms" value="1"
                                        class="mt-1 w-5 h-5 text-[#3FAF4F] border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                        @checked(!empty($consumer->subscribed_sms) || (!empty($consumer->subscribed) && !isset($consumer->subscribed_sms)))>
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800">@lang('Saņemt jaunumus un piedāvājumus telefonā (SMS)')</span>
                                        <span class="block text-xs text-gray-500 mt-0.5">@lang('Ātri paziņojumi un individuāli sagatavoti piedāvājumi SMS veidā')</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-white bg-[#3FAF4F] hover:bg-green-700 rounded-lg shadow transition cursor-pointer">
                                @lang('Saglabāt izmaiņas')
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Applications Section (Altero-style comparison list) --}}
                <div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-[#3FAF4F]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900">@lang('Mani kredītu pieteikumi')</h2>
                                <p class="text-xs sm:text-sm text-gray-500">@lang('Altero salīdzināšanas loģika: šeit redzami visi Jūsu pieteikumi un saņemtie aizdevēju piedāvājumi')</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                            {{ $applications->count() }} {{ trans_choice('pieteikums|pieteikumi|pieteikumu', $applications->count()) }}
                        </span>
                    </div>

                    @if ($applications->count() > 0)
                        <div class="space-y-4">
                            @foreach ($applications as $application)
                                @php
                                    $isReady = $application->completion_percentage >= 80;
                                @endphp
                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 rounded-xl border transition-all duration-150 {{ $isReady ? 'bg-gradient-to-r from-[#F0FDF4] to-white border-green-200 shadow-sm' : 'bg-gray-50 border-gray-200' }}">
                                    <div class="mb-4 md:mb-0 space-y-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ $application->section->name ?? __('Aizdevuma pieteikums') }}
                                            </h3>
                                            <span class="text-xl font-extrabold text-[#3FAF4F]">
                                                {{ number_format($application->amount, 0, '.', ' ') }}&nbsp;{{ currency_symbol() }}
                                            </span>
                                            @if ($isReady)
                                                <span class="px-2.5 py-0.5 text-xs font-bold text-green-800 bg-green-100 rounded-full border border-green-200">
                                                    @lang('Piedāvājumi sagatavoti')
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 text-xs font-bold text-amber-800 bg-amber-100 rounded-full border border-amber-200">
                                                    @lang('Pieteikums nepabeigts') ({{ $application->completion_percentage }}%)
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-4 text-xs sm:text-sm text-gray-500 flex-wrap">
                                            <span><strong>@lang('Termiņš'):</strong> {{ $application->term_months }} @lang('mēn.')</span>
                                            <span>•</span>
                                            <span><strong>@lang('Iesniegts'):</strong> {{ $application->created_at->format('d.m.Y H:i') }}</span>
                                            @if(!empty($application->phone))
                                                <span>•</span>
                                                <span><strong>@lang('Tālrunis'):</strong> {{ $application->phone }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 w-full md:w-auto">
                                        @if ($isReady)
                                            <a href="{{ route('consumers.applications.show', $application->token) }}"
                                                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-[#3FAF4F] hover:bg-green-700 rounded-lg shadow transition">
                                                <span>@lang('Skatīt piedāvājumus')</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('applications.continue', $application->token) }}"
                                                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow transition">
                                                <span>@lang('Turpināt pieteikumu')</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 px-4">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">@lang('Jums vēl nav neviena pieteikuma')</h3>
                            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                                @lang('Aizpildiet aizdevuma pieteikumu, lai saņemtu un salīdzinātu līdz pat 25 aizdevēju piedāvājumus vienuviet.')
                            </p>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white bg-[#3FAF4F] hover:bg-green-700 rounded-lg shadow transition cursor-pointer"
                                @click="open = true">
                                @lang('Pieteikties aizdevumam')
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </main>
@endsection
