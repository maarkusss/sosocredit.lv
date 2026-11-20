@php
    use App\Models\Setting;
    use App\Contracts\HoiFmServiceInterface;
@endphp

@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Settings' }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>


                    Main information
                </h2>

                @if ($logo_path = Setting::retrieve('logo'))
                    <div class="flex items-center justify-start h-12 my-4"
                         id="website-image-container">
                        <img class="object-contain max-w-full max-h-full"
                             id="website-image"
                             src="{{ $logo_path }}"
                             alt="Website logo">
                    </div>
                @else
                    <div class="flex items-center justify-start h-12 my-4 hidden"
                         id="website-image-container">
                        <img class="object-contain max-w-full max-h-full"
                             id="website-image"
                             src=""/>
                    </div>
                @endif

                <div class="form-group-wrapper">

                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Website\'s name',
                        'value_if_auth' => $name ?: '',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'api_key',
                        'name' => 'api_key',
                        'label' => 'API KEY',
                        'placeholder' => 'Website\'s API key (for 3rd parties accessing API)',
                        'value_if_auth' => $api_key ?: '',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'individual_days_epc',
                        'name' => 'individual_days_epc',
                        'label' => 'Individual days EPC',
                        'placeholder' => 'Individual days EPC',
                        'value_if_auth' => $individual_days_epc ?: '',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'prefix',
                        'name' => 'prefix',
                        'label' => 'GoodAff S1',
                        'placeholder' => 'GoodAff S1 param (to access Goodaff API)',
                        'value_if_auth' => $prefix ?: '',
                        'required' => true,
                    ])

                    <label class="flex flex-col flex-auto w-full">
                        <span class="mb-2 text-sm font-medium text-gray-600">Logo</span>
                        <input
                            class="previewUploadableImage px-3 py-2 text-xs text-gray-700 bg-gray-100 border border-gray-300 rounded focus:ring-primary-dark focus:border-primary-dark"
                            name="logo"
                            type="file"
                            data-preview-input="website-image"
                            data-preview-input-container="website-image-container"
                            accept="image/*"
                            id="fileInput">
                    </label>

                    @include('admincp.includes.input', [
                        'id' => 'sitemap',
                        'name' => 'sitemap',
                        'label' => 'Sitemap Crawler link',
                        'placeholder' => 'To generate sitemap (sitemap:generate)',
                        'value_if_auth' => $sitemap ?: '',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'country_code',
                        'type' => 'number',
                        'name' => 'country_code',
                        'label' => 'Country code',
                        'placeholder' => 'Country code (for phone numbers)',
                        'value_if_auth' => $country_code ?: '',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'api_default_zero_percent_value',
                        'type' => 'text',
                        'name' => 'api_default_zero_percent_value',
                        'label' => 'API Default zero_percent_value',
                        'placeholder' => 'Default returned in API /offers',
                        'value_if_auth' => old('api_default_zero_percent_value', $api_default_zero_percent_value),
                    ])

                    @include('admincp.includes.input', [
                          'id' => 'consumer_daily_application_limit',
                          'type' => 'number',
                          'name' => 'consumer_daily_application_limit',
                          'label' => 'Daily application limit per consumer',
                          'placeholder' => $consumer_daily_application_limit ?: 0,
                          'value_if_auth' => old('consumer_daily_application_limit', $consumer_daily_application_limit),
                      ])
                </div>

                <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised mt-10">
                    <img src="/images/hoi.png"
                         title="Hoi.fm"
                         alt="hoi.fm"
                         class="mr-2"
                         style="display: inline; width: 20px; margin-bottom: 1px; filter: invert(.8)" />

                    Hoi.fm
                </h2>

                <div class="form-group-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'hoi_fm_sender_name',
                        'name' => 'hoi_fm_sender_name',
                        'label' => 'Sender Name',
                        'value' => $hoi_fm_sender_name,
                    ])

                    <div class="flex flex-col justify-start items-start text-xs text-gray-400">
                        <label class="input-wrap__label">
                            Preview
                        </label>

                        <div class="flex flex-row justify-start items-center text-xs text-gray-400">
                            <span class="mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>

                            FROM: {{ app(HoiFmServiceInterface::class)->fromHeader() }}
                        </div>
                    </div>
                </div>

                <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised mt-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
                    </svg>


                    Head section codes
                </h2>

                <div class="flex flex-row w-full justify-start items-start gap-6">
                    @include('admincp.includes.textarea', [
                        'id' => 'head_code',
                        'name' => 'head_code',
                        'label' => 'Head code  (appears in <head> on public-layout pages)',
                        'placeholder' => 'Head code',
                        'cols' => 30,
                        'rows' => 14,
                        'value_if_auth' => $head_code ?: '',
                    ])

                    @include('admincp.includes.textarea', [
                        'id' => 'head_code_go',
                        'name' => 'head_code_go',
                        'label' => 'Head code Go (appears in <head> on go page)',
                        'placeholder' => 'Head code Go',
                        'cols' => 30,
                        'rows' => 14,
                        'value_if_auth' => $head_code_go ?: '',
                    ])
                </div>


                <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised mt-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>

                    Static Files
                </h2>

                <div class="flex flex-row w-full justify-start items-start gap-6 mb-10">
                    @include('admincp.includes.textarea', [
                        'id' => 'manifest_json',
                        'name' => 'manifest_json',
                        'label' => 'manifest.json',
                        'placeholder' => '',
                        'cols' => 30,
                        'rows' => 12,
                        'value' => old('manifest_json', $manifest_json),
                    ])

                    @include('admincp.includes.textarea', [
                        'id' => 'robots_txt',
                        'name' => 'robots_txt',
                        'label' => 'robots.txt',
                        'placeholder' => '',
                        'cols' => 30,
                        'rows' => 12,
                        'value' => old('robots_txt', $robots_txt),
                    ])
                </div>

                <div class="form-group-wrapper">
                    <div class="flex flex-col w-full mb-2">
                        <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-5 h-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 01-5.276 3.67m0 0a9 9 0 01-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25"/>
                            </svg>

                            Languages
                        </h2>
                        <div class="alert alert-outline mt-2">Tick the languages to create content for:</div>

                        @foreach (config('app.available_locales') as $locale_name => $locale_key)
                            <div class="checkbox-wrap">
                                <input type="checkbox"
                                       id="locale_{{ $locale_key }}"
                                       name="lang[]"
                                       value="{{ $locale_key }}"
                                       class="checkbox-wrap__checkbox"
                                       @auth
                                           @if ($lang->where('value', $locale_key)->first()) checked @endif
                                    @endauth
                                >
                                <label for="locale_{{ $locale_key }}" class="checkbox-wrap__label">
                                    {{ $locale_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group-wrapper">
                    <div class="flex flex-col w-full mb-2">
                        <h2 class="admin-page-fieldset-title admin-page-fieldset-title_iconised">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                 class="w-5 h-5 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            Options
                        </h2>

                        @include('mobile-apps::lgp.admincp.includes.mobile-apps-settings')

                        <div class="input-wrap my-2">
                            @include('goodday-common::admincp.includes.select', [
                                'id' => 'email_domains_whitelist_enabled',
                                'name' => 'email_domains_whitelist_enabled',
                                'label' => 'Email Domains Whitelist : Enable',
                                'default_option' => '',
                                'options' => [
                                    1 => 'Yes',
                                    0 => 'No',
                                ],
                                'value' => Setting::retrieve('email_domains_whitelist_enabled') ?: 0,
                            ])
                        </div>

                        <div class="text-xs text-gray-400">
                            {{ implode(', ', config('mail.allowed_domains')) }}
                        </div>
                    </div>
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
