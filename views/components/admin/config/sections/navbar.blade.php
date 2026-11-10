{{--
    Admin config: Navbar section.

    Fields: sticky, show_brand_text, show_discord, logo_size.
    Navigation links are managed by Azuriom in admin.navbar-elements.
--}}
@props([
    'active' => false,
])

<x-admin.config.ui.tab
    id="navbar"
    :title="trans('theme::admin.config_nav_navbar')"
    :description="trans('theme::admin.config_desc_navbar')"
    icon="bi-window-stack"
    :active="$active"
>
    <div class="card config-block mb-3">
        <div class="card-body d-flex flex-column gap-3">
            <x-admin.config.ui.switch
                name="navbar.sticky"
                :default="true"
                :label="trans('theme::admin.navbar_sticky')"
                :hint="trans('theme::admin.navbar_sticky_hint')"
            />

            <x-admin.config.ui.switch
                name="navbar.show_brand_text"
                :default="true"
                :label="trans('theme::admin.navbar_brand_text')"
                :hint="trans('theme::admin.navbar_brand_text_hint')"
            />

            <x-admin.config.ui.switch
                name="navbar.show_discord"
                :default="true"
                :label="trans('theme::admin.navbar_discord')"
                :hint="trans('theme::admin.navbar_discord_hint')"
            />

            <x-admin.config.ui.field
                name="navbar.logo_size"
                :label="trans('theme::admin.navbar_logo_size')"
                type="range"
                :default="48"
                :min="32"
                :max="64"
                :hint="trans('theme::admin.navbar_logo_size_hint')"
                :tooltip="trans('theme::admin.navbar_logo_size_tip')"
                col="col-12 col-md-6"
            />
        </div>
    </div>

    <div class="card config-block">
        <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <p class="text-body-secondary mb-0">{{ trans('theme::admin.navbar_links_help') }}</p>

            <a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('admin.navbar-elements.index') }}">
                <i class="bi bi-link-45deg" aria-hidden="true"></i> {{ trans('theme::admin.navbar_links_manage') }}
            </a>
        </div>
    </div>
</x-admin.config.ui.tab>
