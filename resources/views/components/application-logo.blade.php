@props(['is_applicant_page'])

@php
    $logo_url = '/' . app()->getLocale();
@endphp


<a href="{{ $logo_url }}" class="application-logo" aria-label="{{ __('main.go_to_homepage') }}">
    @include('includes.svgs.logo')
</a>
