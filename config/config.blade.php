{{--
    Admin theme config page: shell around the section tabs.

    Expects: $theme (theme being edited) and $images (list of theme images).
    Renders the tab sidebar, the section panes, the error summary and the
    fixed save bar. The three scripts are loaded at the end, in defer order.
--}}
@extends('admin.layouts.admin')

@section('title', trans('theme::admin.config_title'))

@php
    $themeVersion = themes()->findDescription(themes()->currentTheme())?->version ?? '1';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ theme_asset('css/admin.css') }}&amp;ver={{ $themeVersion }}">
@endpush

@php
    $configTabs = [
        'appearance' => ['label' => trans('theme::admin.config_nav_appearance'), 'icon' => 'bi-brush-fill'],
        'navbar' => ['label' => trans('theme::admin.config_nav_navbar'), 'icon' => 'bi-window-stack'],
        'footer' => ['label' => trans('theme::admin.config_nav_footer'), 'icon' => 'bi-card-text'],
        'hero' => ['label' => trans('theme::admin.config_nav_hero'), 'icon' => 'bi-stars'],
        'about' => ['label' => trans('theme::admin.config_nav_about'), 'icon' => 'bi-info-circle'],
        'news' => ['label' => trans('theme::admin.config_nav_news'), 'icon' => 'bi-newspaper'],
        'stats' => ['label' => trans('theme::admin.config_nav_stats'), 'icon' => 'bi-bar-chart-fill'],
        'discord' => ['label' => trans('theme::admin.config_nav_discord'), 'icon' => 'bi-discord'],
    ];

    $configNavGroups = [
        ['label' => trans('theme::admin.config_group_components'), 'tabs' => ['appearance', 'navbar', 'footer']],
        ['label' => trans('theme::admin.config_group_home'), 'tabs' => ['hero', 'about', 'news', 'stats', 'discord']],
    ];

    $configErrorCounts = [];

    // Map every validation error to the tab that owns its key, then count them.
    foreach ($errors->keys() as $configErrorKey) {
        $configErrorSegment = explode('.', $configErrorKey)[0];
        // Colors and font keys live in the Appearance tab, not in a tab of their own.
        $configErrorTab = in_array($configErrorSegment, ['colors', 'font'], true)
            ? 'appearance'
            : (isset($configTabs[$configErrorSegment]) ? $configErrorSegment : 'appearance');

        $configErrorCounts[$configErrorTab] = ($configErrorCounts[$configErrorTab] ?? 0) + 1;
    }

    // Whitelist known tab ids, in nav order, for the JS error handling.
    $configErrorTabList = array_values(array_intersect(array_keys($configTabs), array_keys($configErrorCounts)));
@endphp

@section('content')
    <form action="{{ route('admin.themes.config', $theme) }}" method="POST" id="themeConfigForm" novalidate>
        @csrf

        <noscript>
            <style>
                .config-sidebar { display: none !important; }
                .config-content { flex: 0 0 100% !important; max-width: 100% !important; width: 100% !important; }
                .tab-content > .tab-pane { display: block !important; opacity: 1 !important; }
            </style>

            <div class="alert alert-info" role="status">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                {{ trans('theme::admin.config_noscript') }}
            </div>
        </noscript>

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <h2 class="h5 alert-heading">
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                    {{ trans('theme::admin.config_errors_title') }}
                </h2>

                <p class="mb-2">{{ trans('theme::admin.config_errors_help') }}</p>

                <ul class="mb-0">
                    @foreach($configErrorTabList as $configErrorTab)
                        <li>
                            <a href="#tab-{{ $configErrorTab }}" data-config-error-link="{{ $configErrorTab }}">
                                {{ $configTabs[$configErrorTab]['label'] }}
                            </a>

                            <span class="badge text-bg-danger">
                                {{ $configErrorCounts[$configErrorTab] }}
                                <span class="visually-hidden"> {{ trans('theme::admin.config_errors_badge') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="pokedex p-3">
            <div class="pokedex-top d-flex align-items-center gap-2 px-1 pt-1 pb-3" aria-hidden="true">
                <span class="pokedex-lens flex-shrink-0"></span>
                <span class="pokedex-light flex-shrink-0 bg-danger text-danger"></span>
                <span class="pokedex-light flex-shrink-0 bg-warning text-warning"></span>
                <span class="pokedex-light flex-shrink-0 bg-success text-success"></span>
            </div>

            <div class="card pokedex-screen">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <aside class="col-12 col-lg-3 config-sidebar">
                            <div class="config-sidebar-inner p-2 p-lg-3">
                                <nav
                                    class="nav nav-pills config-tabs flex-lg-column flex-nowrap gap-1"
                                    role="tablist"
                                    aria-orientation="vertical"
                                    aria-label="{{ trans('theme::admin.config_tablist_label') }}"
                                    {{-- Read by config-tabs.js to open the first tab with errors. --}}
                                    data-config-errors='@json($configErrorTabList)'
                                >
                                    @foreach($configNavGroups as $configNavGroup)
                                        @if($configNavGroup['label'])
                                            <div class="config-nav-group d-none d-lg-block px-3 pt-3 pb-1 text-uppercase">{{ $configNavGroup['label'] }}</div>
                                        @endif

                                        @foreach($configNavGroup['tabs'] as $configTabId)
                                            <button
                                                type="button"
                                                class="nav-link d-flex align-items-center gap-2 rounded-3 px-3 py-2 text-start @if($configTabId === 'appearance') active @endif"
                                                id="tab-{{ $configTabId }}-tab"
                                                data-bs-toggle="pill"
                                                data-bs-target="#tab-{{ $configTabId }}"
                                                role="tab"
                                                aria-controls="tab-{{ $configTabId }}"
                                                aria-selected="{{ $configTabId === 'appearance' ? 'true' : 'false' }}"
                                                data-config-tab="{{ $configTabId }}"
                                            >
                                                <i class="bi {{ $configTabs[$configTabId]['icon'] }}" aria-hidden="true"></i>
                                                <span>{{ $configTabs[$configTabId]['label'] }}</span>

                                                @if(isset($configErrorCounts[$configTabId]))
                                                    <span class="badge text-bg-danger ms-auto">
                                                        {{ $configErrorCounts[$configTabId] }}
                                                        <span class="visually-hidden"> {{ trans('theme::admin.config_errors_badge') }}</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endforeach
                                    @endforeach
                                </nav>
                            </div>
                        </aside>

                        <div class="col-12 col-lg-9 config-content p-3 p-lg-4">
                            <div class="tab-content" id="configTabContent">
                                <x-admin.config.sections.appearance :active="true" />
                                <x-admin.config.sections.navbar />
                                <x-admin.config.sections.footer />
                                <x-admin.config.sections.hero :images="$images ?? []" />
                                <x-admin.config.sections.about :images="$images ?? []" />
                                <x-admin.config.sections.news />
                                <x-admin.config.sections.stats />
                                <x-admin.config.sections.discord />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pokedex-bottom d-flex justify-content-center gap-1 pt-3" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </div>

        <div class="config-save-bar position-fixed bottom-0 end-0 z-3 d-flex flex-column align-items-stretch gap-2 m-3 p-3 bg-body border rounded-3 shadow">
            <div class="config-save-author d-flex align-items-center gap-2">
                <i class="bi bi-code-slash text-primary fs-5" aria-hidden="true"></i>
                <span>
                    <small class="d-block lh-1">{{ trans('theme::admin.config_author') }}</small>
                    <a href="https://github.com/clementbo50" class="fw-semibold" target="_blank" rel="noopener noreferrer">clementbo</a>
                </span>
            </div>

            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" data-config-save>
                <span class="spinner-border spinner-border-sm" data-config-save-spinner hidden aria-hidden="true"></span>
                <i class="bi bi-floppy-fill" aria-hidden="true"></i>
                {{ trans('messages.actions.save') }}
            </button>
        </div>
    </form>

    @push('footer-scripts')
        <script src="{{ theme_asset('js/admin/config-tabs.js') }}&amp;ver={{ $themeVersion }}" defer></script>
        <script src="{{ theme_asset('js/admin/config-fields.js') }}&amp;ver={{ $themeVersion }}" defer></script>
        <script src="{{ theme_asset('js/admin/config-repeaters.js') }}&amp;ver={{ $themeVersion }}" defer></script>
    @endpush
@endsection
