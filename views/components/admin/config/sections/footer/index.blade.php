{{--
    Admin config: Footer section.

    Fields: title, description, legal, social_pokeball, links (repeater of
    name, value and new_tab).
--}}
@props([
    'active' => false,
])

@php
    $footerLinks = old('footer.links', theme_config('footer.links') ?? []);

    if (! is_array($footerLinks) || $footerLinks === []) {
        $footerLinks = [['name' => '', 'value' => '/', 'new_tab' => false]];
    }

    // Reindex so row keys stay contiguous even after a row was removed.
    $footerLinks = array_values($footerLinks);
@endphp

<x-admin.config.ui.tab
    id="footer"
    :title="trans('theme::admin.footer_title')"
    :description="trans('theme::admin.config_desc_footer')"
    icon="bi-card-text"
    :active="$active"
>
    <div class="card config-block mb-3">
        <div class="card-body">
            <div class="row g-3">
                <x-admin.config.ui.field
                    name="footer.title"
                    :label="trans('theme::admin.footer_name')"
                    :maxlength="100"
                    :placeholder="trans('theme::theme.footer_name_placeholder')"
                    :hint="trans('theme::admin.footer_name_hint', ['max' => 100])"
                />

                <x-admin.config.ui.field
                    name="footer.description"
                    :label="trans('theme::admin.footer_desc')"
                    type="textarea"
                    :maxlength="300"
                    :placeholder="trans('theme::theme.footer_desc_placeholder')"
                    :hint="trans('theme::admin.footer_desc_hint', ['max' => 300])"
                />

                <x-admin.config.ui.field
                    name="footer.legal"
                    :label="trans('theme::admin.footer_legal')"
                    :maxlength="150"
                    :placeholder="trans('theme::admin.footer_legal_placeholder')"
                    :hint="trans('theme::admin.footer_legal_hint', ['max' => 150])"
                />
            </div>

            <x-admin.config.ui.switch
                name="footer.social_pokeball"
                :default="true"
                :label="trans('theme::admin.footer_social')"
                :hint="trans('theme::admin.footer_social_hint')"
                class="mt-3"
            />
        </div>
    </div>

    <div class="card config-block">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <h3 class="h5 mb-0">{{ trans('theme::admin.footer_links') }}</h3>

                <span role="img" tabindex="0" aria-label="{{ trans('theme::admin.footer_links_tip') }}" data-bs-toggle="tooltip" title="{{ trans('theme::admin.footer_links_tip') }}">
                    <i class="bi bi-info-circle-fill text-primary" aria-hidden="true"></i>
                </span>
            </div>

            <x-admin.config.ui.repeater
                id="footerLinksList"
                name="footer.links"
                row="sections.footer.link-row"
                row-class="footer-link-row"
                :rows="$footerLinks"
                :min="1"
                :add-label="trans('theme::admin.footer_link_add')"
                add-id="addFooterLinkButton"
                template-id="footerLinkTemplate"
            />
        </div>
    </div>

    <div class="card config-block mt-3">
        <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <p class="text-body-secondary mb-0">{{ trans('theme::admin.footer_logo_help') }}</p>

            <a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('admin.settings.index') }}">
                <i class="bi bi-image-fill" aria-hidden="true"></i> {{ trans('theme::admin.footer_logo_manage') }}
            </a>
        </div>
    </div>
</x-admin.config.ui.tab>
