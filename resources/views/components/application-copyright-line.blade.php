@props(['include_developer', 'is_applicant_page'])

<div class="mt-4 text-xs font-medium leading-none text-[#7C8381]">
    @php
        $site_year = config('app.site_creation_year');
        $current_year = date('Y');
        $year_line = $site_year;
        if ($current_year != $site_year && $current_year > $site_year) {
            $year_line .= ' - ' . $current_year;
        }
    @endphp
    © {{ $year_line }}
    <a href="{{ route('home.index') }}"
        class="text-xs leading-none font-medium @if (!empty($is_applicant_page)) text-loan-normal hover:text-loan-dark @else text_primary link_primary @endif hover:underline">{{ config('app.site_domain') }}</a>
</div>

@if (!empty($include_developer))
    <div class="text-xs font-medium text-[#7C8381]">
        @if (!empty(config('app.footer_address')))
            <address class="not-italic" style="font-style: normal;">@lang('Address'): {{ config('app.footer_address') }}
            </address>
        @endif
    </div>
@endif
