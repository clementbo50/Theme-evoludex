{{--
    Admin config: Appearance section (colors and font).

    Fields: colors.light_*, colors.dark_*, font.toggle, font.url, font.text.
    The two palettes below are only the defaults shown for unsaved colors.
--}}
@props([
    'active' => false,
])

@php
    $lightColors = [
        ['colors.light_primary', trans('theme::admin.primary'), '#2B5FA6'],
        ['colors.light_secondary', trans('theme::admin.secondary'), '#9C3FA8'],
        ['colors.light_bg', trans('theme::admin.background'), '#F7F4EC'],
    ];

    $darkColors = [
        ['colors.dark_primary', trans('theme::admin.primary'), '#7FB0F0'],
        ['colors.dark_secondary', trans('theme::admin.secondary'), '#D08ADE'],
        ['colors.dark_bg', trans('theme::admin.background'), '#101828'],
    ];
@endphp

<x-admin.config.ui.tab
    id="appearance"
    :title="trans('theme::admin.config_nav_appearance')"
    :description="trans('theme::admin.config_desc_appearance')"
    icon="bi-brush-fill"
    :active="$active"
>
    <x-slot:titleAction>
        <span role="img" tabindex="0" aria-label="{{ trans('theme::admin.colors_tip') }}" data-bs-toggle="tooltip" title="{{ trans('theme::admin.colors_tip') }}">
            <i class="bi bi-info-circle-fill text-primary" aria-hidden="true"></i>
        </span>
    </x-slot:titleAction>

    <div class="card config-block mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="config-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0 lh-1 fs-5" aria-hidden="true">
                    <i class="bi bi-sun-fill text-warning"></i>
                </span>
                <h3 class="h5 mb-0">{{ trans('theme::admin.section_light') }}</h3>
            </div>

            <p class="text-body-secondary">{{ trans('theme::admin.section_light_hint') }}</p>

            <div class="row g-2">
                @foreach($lightColors as [$colorName, $colorLabel, $colorDefault])
                    <x-admin.config.ui.color :name="$colorName" :label="$colorLabel" :default="$colorDefault" />
                @endforeach
            </div>
        </div>
    </div>

    <div class="card config-block mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="config-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0 lh-1 fs-5" aria-hidden="true">
                    <i class="bi bi-moon-stars-fill text-primary"></i>
                </span>
                <h3 class="h5 mb-0">{{ trans('theme::admin.section_dark') }}</h3>
            </div>

            <p class="text-body-secondary">{{ trans('theme::admin.section_dark_hint') }}</p>

            <div class="row g-2">
                @foreach($darkColors as [$colorName, $colorLabel, $colorDefault])
                    <x-admin.config.ui.color :name="$colorName" :label="$colorLabel" :default="$colorDefault" />
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-3">
        <a class="btn btn-sm btn-outline-primary text-nowrap" href="https://coolors.co/" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-palette" aria-hidden="true"></i> {{ trans('theme::admin.palette_link') }}
        </a>

        <a class="btn btn-sm btn-outline-primary text-nowrap" href="https://webaim.org/resources/contrastchecker/" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-contrast" aria-hidden="true"></i> {{ trans('theme::admin.contrast_tool') }}
        </a>
    </div>

    <div class="card config-block mt-3">
        <div class="card-body">
            <h3 class="h5 mb-3">{{ trans('theme::admin.font_title') }}</h3>

            <div data-font-toggle>
                <x-admin.config.ui.switch
                    name="font.toggle"
                    :default="true"
                    :label="trans('theme::admin.font_toggle')"
                />

                <div class="row g-3 mt-1" data-font-fields>
                    <x-admin.config.ui.field
                        name="font.url"
                        :label="trans('theme::admin.font_url')"
                        type="url"
                        :default="'https://fonts.bunny.net/css?family=nunito:400,700&display=swap'"
                        :placeholder="'https://fonts.bunny.net/css?family=nunito:400,700&display=swap'"
                        :hint="trans('theme::admin.font_url_hint')"
                        hint-url="https://fonts.bunny.net/"
                        :hint-link-label="trans('theme::admin.font_find')"
                        col="col-12 col-lg-6"
                    />

                    <x-admin.config.ui.field
                        name="font.text"
                        :label="trans('theme::admin.font_text')"
                        :default="'Nunito'"
                        placeholder="Nunito"
                        :maxlength="50"
                        :hint="trans('theme::admin.font_text_hint')"
                        col="col-12 col-lg-6"
                    />
                </div>
            </div>
        </div>
    </div>
</x-admin.config.ui.tab>
