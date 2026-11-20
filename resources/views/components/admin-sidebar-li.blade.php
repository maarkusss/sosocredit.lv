@props(['id', 'link', 'route_name', 'target', 'title'])

@php
    if (empty($link) && !empty($route_name)) {
        $link = route($route_name);
    }
@endphp

<li class="sidebar__li">
    <a href="{{ $link }}" @if (!empty($target)) target="{{ $target }}" @endif
        @if (!empty($id)) id="{{ $id }}" @endif
        class="sidebar__link @if (!empty($route_name) && request()->route()->getName() == $route_name) sidebar__link_active @endif">
        <div class="flex flex-row items-center">
            {{ $slot }}
            <span class="font-medium text-normal">{{ $title }}</span>
        </div>
    </a>
</li>
