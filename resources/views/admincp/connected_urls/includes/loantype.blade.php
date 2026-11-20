<div class="content-wrapper">
    <div class="content-wrapper__form-wrapper">
        <h2 class="admin-page-fieldset-title">Connected URLs</h2>
        <div class="admin-page-fieldset-wrapper_last">
            <form action="{{ route('admincp.connected-urls.store') }}" method="POST">
                @csrf

                <div class="flex flex-row justify-start mb-4">
                    <div class="w-16 mr-2">
                        @include('admincp.includes.input', [
                            'id' => 'current_locale',
                            'name' => 'current_locale',
                            'value' => app()->getLocale(),
                            'class' => 'input-wrap__input_dark cursor-disabled',
                            'label' => 'Lang',
                            'readonly' => true,
                        ])
                    </div>

                    <div class="w-1/2">
                        @include('admincp.includes.input', [
                            'id' => 'url',
                            'name' => 'url',
                            'value' => str_replace(config('app.url'), '', $loantype->canonical_url()),
                            'class' => 'input-wrap__input_dark cursor-disabled',
                            'label' => 'URL',
                            'readonly' => true,
                        ])
                    </div>
                </div>

                @foreach (inactive_locales() as $locale)
                    <div class="flex flex-row justify-start mb-4">
                        <div class="w-16 mr-2">
                            @include('admincp.includes.input', [
                                'id' => 'locale_' . $locale,
                                'name' => 'related_locales[' . $locale . ']',
                                'value' => $locale,
                                'class' => 'input-wrap__input_dark',
                                'readonly' => true,
                            ])
                        </div>

                        <div class="w-1/2">
                            @include('admincp.includes.input', [
                                'id' => 'locale_' . $locale,
                                'name' => 'related_urls[' . $locale . ']',
                                'value' => $related_urls[$locale] ?? '',
                            ])
                        </div>
                    </div>
                @endforeach

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save Connected URLs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
