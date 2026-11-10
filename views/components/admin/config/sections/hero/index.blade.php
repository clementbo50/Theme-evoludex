{{--
    Admin config: Hero section.

    Fields: visible, show_description, description, bg (image), buttons
    (1 to 4 repeater rows of type, label, value and new_tab).
--}}
@props([
    'images' => [],
    'active' => false,
])

@php
    $heroButtons = old('hero.buttons', theme_config('hero.buttons') ?? []);

    if (! is_array($heroButtons) || $heroButtons === []) {
        $heroButtons = [['type' => 'copy', 'label' => '', 'value' => '', 'new_tab' => false]];
    }

    // Reindex so row keys stay contiguous even after a row was removed.
    $heroButtons = array_values($heroButtons);
@endphp

<x-admin.config.ui.tab
    id="hero"
    :title="trans('theme::admin.hero_title')"
    :description="trans('theme::admin.config_desc_hero')"
    icon="bi-stars"
    :active="$active"
>
    <div class="card config-block mb-3">
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                <x-admin.config.ui.switch
                    name="hero.visible"
                    :default="true"
                    :label="trans('theme::admin.hero_visible')"
                    :hint="trans('theme::admin.hero_visible_hint')"
                />

                <x-admin.config.ui.switch
                    name="hero.show_description"
                    :default="true"
                    :label="trans('theme::admin.hero_show_desc')"
                />
            </div>

            <div class="row g-3 mt-1">
                <x-admin.config.ui.field
                    name="hero.description"
                    :label="trans('theme::admin.hero_desc')"
                    type="textarea"
                    :maxlength="300"
                    :hint="trans('theme::admin.hero_desc_hint', ['max' => 300])"
                    col="col-12 col-lg-6"
                />

                <x-admin.config.ui.image
                    name="hero.bg"
                    :label="trans('theme::admin.hero_bg')"
                    :images="$images ?? []"
                    :hint="trans('theme::admin.hero_bg_hint')"
                    col="col-12 col-lg-6"
                />
            </div>
        </div>
    </div>

    <div class="card config-block">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <h3 class="h5 mb-0">{{ trans('theme::admin.hero_buttons') }}</h3>

                <span role="img" tabindex="0" aria-label="{{ trans('theme::admin.hero_buttons_tip') }}" data-bs-toggle="tooltip" title="{{ trans('theme::admin.hero_buttons_tip') }}">
                    <i class="bi bi-info-circle-fill text-primary" aria-hidden="true"></i>
                </span>
            </div>

            <x-admin.config.ui.repeater
                id="heroButtonsList"
                name="hero.buttons"
                row="sections.hero.button-row"
                row-class="hero-button-row"
                :rows="$heroButtons"
                :min="1"
                :max="4"
                :add-label="trans('theme::admin.hero_button_add')"
                add-id="addHeroButton"
                template-id="heroButtonTemplate"
            />
        </div>
    </div>

    <div class="card config-block mt-3">
        <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <p class="text-body-secondary mb-0">{{ trans('theme::admin.hero_logo_help') }}</p>

            <a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('admin.settings.index') }}">
                <i class="bi bi-image-fill" aria-hidden="true"></i> {{ trans('theme::admin.hero_logo_manage') }}
            </a>
        </div>
    </div>
</x-admin.config.ui.tab>
