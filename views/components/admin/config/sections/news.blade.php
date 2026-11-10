{{--
    Admin config: News section.

    Fields: visible, show_title, title, show_description, description,
    show_author, show_date.
--}}
@props([
    'active' => false,
])

<x-admin.config.ui.tab
    id="news"
    :title="trans('theme::admin.news_title')"
    :description="trans('theme::admin.config_desc_news')"
    icon="bi-newspaper"
    :active="$active"
>
    <div class="card config-block">
        <div class="card-body">
            <x-admin.config.ui.switch
                name="news.visible"
                :default="true"
                :label="trans('theme::admin.news_visible')"
                :hint="trans('theme::admin.news_visible_hint')"
                class="mb-3"
            />

            <div class="row g-3">
                <x-admin.config.ui.field
                    name="news.title"
                    :label="trans('theme::admin.news_name')"
                    :maxlength="100"
                    :placeholder="trans('theme::admin.news_name_placeholder')"
                    :hint="trans('theme::admin.news_name_hint', ['max' => 100])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="news.show_title"
                            :default="true"
                            :label="trans('theme::admin.show_title')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>

                <x-admin.config.ui.field
                    name="news.description"
                    :label="trans('theme::admin.news_desc')"
                    type="textarea"
                    :maxlength="300"
                    :placeholder="trans('theme::admin.news_desc_placeholder')"
                    :hint="trans('theme::admin.news_desc_hint', ['max' => 300])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="news.show_description"
                            :default="true"
                            :label="trans('theme::admin.show_description')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>
            </div>

            <p class="form-label mt-3 mb-2">{{ trans('theme::admin.news_meta_visibility') }}</p>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <x-admin.config.ui.switch
                        name="news.show_author"
                        :default="true"
                        :label="trans('theme::admin.news_show_author')"
                    />
                </div>

                <div class="col-12 col-md-6">
                    <x-admin.config.ui.switch
                        name="news.show_date"
                        :default="true"
                        :label="trans('theme::admin.news_show_date')"
                    />
                </div>
            </div>
        </div>
    </div>
</x-admin.config.ui.tab>
