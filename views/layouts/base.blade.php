<!DOCTYPE html>
@include('elements.base')
@php
    $themeVersion = themes()->findDescription(themes()->currentTheme())?->version ?? '1';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', setting('description', ''))">
    <meta name="theme-color" content="#E3350D">
    <meta name="author" content="Azuriom">

    <meta property="og:title" content="@yield('title')">
    <meta property="og:type" content="@yield('type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ favicon() }}">
    <meta property="og:description" content="@yield('description', setting('description', ''))">
    <meta property="og:site_name" content="{{ site_name() }}">
    @stack('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ site_name() }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ favicon() }}">

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('vendor/axios/axios.min.js') }}" defer></script>
    <script src="{{ asset('js/script.js') }}" defer></script>
    <!-- Page level scripts -->
    @stack('scripts')

    <!-- Fonts -->
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('css/base.css') }}&amp;ver={{ $themeVersion }}" rel="stylesheet">
    <link href="{{ theme_asset('css/overrides_bootsrap.css') }}&amp;ver={{ $themeVersion }}" rel="stylesheet">
    <link href="{{ theme_asset('css/layout.css') }}&amp;ver={{ $themeVersion }}" rel="stylesheet">
    <link href="{{ theme_asset('css/components.css') }}&amp;ver={{ $themeVersion }}" rel="stylesheet">
    @stack('styles')
    @php
        // A value saved before the rule existed could still break out of the <style> block.
        $pokeFont = theme_config('font.text', 'Nunito');
        $pokeFont = is_string($pokeFont) && preg_match('/^[\p{L}\p{N} \-]+$/u', $pokeFont) === 1 ? $pokeFont : 'Nunito';
    @endphp
    @if(theme_config('font.toggle', true) && theme_config('font.url'))
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="{{ theme_config('font.url') }}" rel="stylesheet">
        <style>
            :root {
                --poke-font: '{{ $pokeFont }}';
            }
        </style>
    @endif

    @php
        $pokeDefaults = [
            'light_primary' => '#2B5FA6',
            'light_secondary' => '#9C3FA8',
            'light_bg' => '#F7F4EC',
            'dark_primary' => '#7FB0F0',
            'dark_secondary' => '#D08ADE',
            'dark_bg' => '#101828',
        ];
        $pokeStored = theme_config('colors');

        if (! is_array($pokeStored)) {
            $pokeStored = [];
        }

        $poke = function ($key) use ($pokeStored, $pokeDefaults) {
            $value = $pokeStored[$key] ?? null;
            return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $pokeDefaults[$key];
        };

    @endphp
    <style>
        :root,
        [data-bs-theme="light"] {
            --bs-primary: {{ $poke('light_primary') }};
            --bs-primary-rgb: {{ color_rgb($poke('light_primary')) }};
            --bs-primary-text-emphasis: {{ color_shade($poke('light_primary'), 0.6) }};
            --bs-primary-bg-subtle: {{ color_tint($poke('light_primary'), 0.8) }};
            --bs-primary-border-subtle: {{ color_tint($poke('light_primary'), 0.6) }};
            --bs-secondary: {{ $poke('light_secondary') }};
            --bs-secondary-rgb: {{ color_rgb($poke('light_secondary')) }};
            --bs-secondary-text-emphasis: {{ color_shade($poke('light_secondary'), 0.6) }};
            --bs-secondary-bg-subtle: {{ color_tint($poke('light_secondary'), 0.8) }};
            --bs-secondary-border-subtle: {{ color_tint($poke('light_secondary'), 0.6) }};
            --bs-body-bg: {{ $poke('light_bg') }};
            --bs-body-bg-rgb: {{ color_rgb($poke('light_bg')) }};
            --bs-body-color: {{ color_contrast($poke('light_bg')) }};
            --bs-body-color-rgb: {{ color_rgb(color_contrast($poke('light_bg'))) }};
            --bs-link-color: {{ color_contrast($poke('light_bg')) }};
            --bs-link-color-rgb: {{ color_rgb(color_contrast($poke('light_bg'))) }};
            --bs-link-hover-color: {{ color_contrast($poke('light_bg')) }};
            --bs-link-hover-color-rgb: {{ color_rgb(color_contrast($poke('light_bg'))) }};
            --bs-primary-color-contrast: {{ color_contrast($poke('light_primary')) }};
            --bs-secondary-color-contrast: {{ color_contrast($poke('light_secondary')) }};
            --bs-focus-ring-color: rgba({{ color_rgb($poke('light_primary')) }}, 0.25);
        }

        [data-bs-theme="dark"] {
            --bs-primary: {{ $poke('dark_primary') }};
            --bs-primary-rgb: {{ color_rgb($poke('dark_primary')) }};
            --bs-primary-text-emphasis: {{ color_shade($poke('dark_primary'), 0.4) }};
            --bs-primary-bg-subtle: {{ color_shade($poke('dark_primary'), 0.8) }};
            --bs-primary-border-subtle: {{ color_tint($poke('dark_primary'), 0.4) }};
            --bs-secondary: {{ $poke('dark_secondary') }};
            --bs-secondary-rgb: {{ color_rgb($poke('dark_secondary')) }};
            --bs-secondary-text-emphasis: {{ color_shade($poke('dark_secondary'), 0.4) }};
            --bs-secondary-bg-subtle: {{ color_shade($poke('dark_secondary'), 0.8) }};
            --bs-secondary-border-subtle: {{ color_tint($poke('dark_secondary'), 0.4) }};
            --bs-body-bg: {{ $poke('dark_bg') }};
            --bs-body-bg-rgb: {{ color_rgb($poke('dark_bg')) }};
            --bs-body-color: {{ color_contrast($poke('dark_bg')) }};
            --bs-body-color-rgb: {{ color_rgb(color_contrast($poke('dark_bg'))) }};
            --bs-link-color: {{ color_contrast($poke('dark_bg')) }};
            --bs-link-color-rgb: {{ color_rgb(color_contrast($poke('dark_bg'))) }};
            --bs-link-hover-color: {{ color_contrast($poke('dark_bg')) }};
            --bs-link-hover-color-rgb: {{ color_rgb(color_contrast($poke('dark_bg'))) }};
            --bs-primary-color-contrast: {{ color_contrast($poke('dark_primary')) }};
            --bs-secondary-color-contrast: {{ color_contrast($poke('dark_secondary')) }};
            --bs-focus-ring-color: rgba({{ color_rgb($poke('dark_primary')) }}, 0.25);
        }
    </style>
</head>

<body class="d-flex flex-column" @if(dark_theme()) data-bs-theme="dark" @endif>
<div id="app" class="shrink-0">
    <header class="{{ theme_config('navbar.sticky', true) ? 'sticky-top' : '' }}">
        @include('elements.navbar')
    </header>

    @yield('app')
</div>

@include('partials.footer')

@stack('footer-scripts')

</body>
</html>
