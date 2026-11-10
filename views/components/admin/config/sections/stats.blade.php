{{--
    Admin config: Stats section.

    Fields: visible, title/show_title, description/show_description,
    discord_mode, icon_backgrounds.{players,members,discord}.
--}}
@props([
    'active' => false,
])

@php
    // Key, label and fallback color of the three stat icon backgrounds.
    $statsIcons = [
        ['stats.icon_backgrounds.players', trans('theme::admin.stats_icon_players'), '#FBE0DC'],
        ['stats.icon_backgrounds.members', trans('theme::admin.stats_icon_members'), '#E4E2FA'],
        ['stats.icon_backgrounds.discord', trans('theme::admin.stats_icon_discord'), '#DDF3E6'],
    ];
@endphp

<x-admin.config.ui.tab
    id="stats"
    :title="trans('theme::admin.stats_title')"
    :description="trans('theme::admin.config_desc_stats')"
    icon="bi-bar-chart-fill"
    :active="$active"
>
    <div class="card config-block">
        <div class="card-body">
            <x-admin.config.ui.switch
                name="stats.visible"
                :default="true"
                :label="trans('theme::admin.stats_visible')"
                :hint="trans('theme::admin.stats_visible_hint')"
                class="mb-3"
            />

            <div class="row g-3">
                <x-admin.config.ui.field
                    name="stats.title"
                    :label="trans('theme::admin.stats_name')"
                    :maxlength="100"
                    :placeholder="trans('theme::admin.stats_name_placeholder')"
                    :hint="trans('theme::admin.stats_name_hint', ['max' => 100])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="stats.show_title"
                            :default="true"
                            :label="trans('theme::admin.show_title')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>

                <x-admin.config.ui.field
                    name="stats.description"
                    :label="trans('theme::admin.stats_desc')"
                    type="textarea"
                    :maxlength="300"
                    :placeholder="trans('theme::admin.stats_desc_placeholder')"
                    :hint="trans('theme::admin.stats_desc_hint', ['max' => 300])"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="stats.show_description"
                            :default="true"
                            :label="trans('theme::admin.show_description')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>

                <x-admin.config.ui.field
                    name="stats.discord_mode"
                    :label="trans('theme::admin.stats_discord_mode')"
                    type="select"
                    :default="'registered'"
                    :options="[
                        'registered' => trans('theme::admin.stats_mode_registered'),
                        'active' => trans('theme::admin.stats_mode_active'),
                    ]"
                    :hint="trans('theme::admin.stats_discord_mode_hint')"
                    :tooltip="trans('theme::admin.stats_discord_help')"
                    col="col-12 col-lg-6"
                />
            </div>

            <p class="form-label mt-3 mb-2">{{ trans('theme::admin.stats_icons') }}</p>

            <div class="row g-2">
                @foreach($statsIcons as [$iconName, $iconLabel, $iconDefault])
                    <x-admin.config.ui.color
                        :name="$iconName"
                        :label="$iconLabel"
                        :default="$iconDefault"
                        col="col-12 col-md-4"
                    />
                @endforeach
            </div>

            <small class="form-text d-block">{{ trans('theme::admin.stats_icons_hint') }}</small>

            @error('stats.icon_backgrounds')
                <span class="text-danger small d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</x-admin.config.ui.tab>
