@php $logo = apply_filters('real_estate_dashboard_logo', theme_option('logo')); @endphp

@if ($logo)
    <a href="{{ route('public.index') }}" title="{{ $siteTitle = theme_option('site_title') }}">
        <img
            src="{{ RvMedia::getImageUrl($logo) }}"
            alt="{{ $siteTitle }}"
            height="40"
        >
    </a>
@endif
