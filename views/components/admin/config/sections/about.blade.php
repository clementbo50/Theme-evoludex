{{--
    Admin config: About section.

    Fields: visible, show_title, title, show_description, description, image, caption.
    Depends on: ui.tab, ui.field, ui.switch, ui.image.
--}}
@props([
    'images' => [],
    'active' => false,
])

<x-admin.config.ui.tab
    id="about"
    :title="trans('theme::admin.about_title')"
    :description="trans('theme::admin.config_desc_about')"
    icon="bi-info-circle"
    :active="$active"
>
    <div class="card config-block">
        <div class="card-body">
            <x-admin.config.ui.switch
                name="about.visible"
                :default="true"
                :label="trans('theme::admin.about_visible')"
                :hint="trans('theme::admin.about_visible_hint')"
                class="mb-3"
            />

            <div class="row g-3">
                <x-admin.config.ui.field
                    name="about.title"
                    :label="trans('theme::admin.about_name')"
                    :maxlength="100"
                    :hint="trans('theme::admin.about_name_hint', ['max' => 100])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="about.show_title"
                            :default="true"
                            :label="trans('theme::admin.show_title')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>

                <x-admin.config.ui.field
                    name="about.description"
                    :label="trans('theme::admin.about_desc')"
                    type="textarea"
                    :maxlength="300"
                    :hint="trans('theme::admin.about_desc_hint', ['max' => 300])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="about.show_description"
                            :default="true"
                            :label="trans('theme::admin.show_description')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>

                <x-admin.config.ui.image
                    name="about.image"
                    :label="trans('theme::admin.about_image')"
                    :images="$images ?? []"
                    :hint="trans('theme::admin.about_image_hint')"
                    col="col-12 col-lg-6"
                />

                <x-admin.config.ui.field
                    name="about.caption"
                    :label="trans('theme::admin.about_caption')"
                    :maxlength="60"
                    :hint="trans('theme::admin.about_caption_hint', ['max' => 60])"
                    :tooltip="trans('theme::admin.about_caption_tip')"
                    col="col-12 col-lg-6"
                />
            </div>
        </div>
    </div>
</x-admin.config.ui.tab>
