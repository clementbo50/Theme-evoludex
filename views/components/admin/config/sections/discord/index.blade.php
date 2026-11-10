{{--
    Admin config: Discord section.

    Fields: visible, invite, title/show_title, highlights (1 to 3 rows of
    label, icon and color). The server name, members and icon come from the
    Discord API and cannot be edited here.
--}}
@props([
    'active' => false,
])

@php
    $discordDefaultIcons = ['bi-trophy-fill', 'bi-chat-dots-fill', 'bi-hash'];
    $discordDefaultColors = ['#FBE0DC', '#E4E2FA', '#DDF3E6'];

    $discordHighlights = old('discord.highlights', theme_config('discord.highlights'));
    $discordHighlights = is_array($discordHighlights) ? array_values($discordHighlights) : [];

    if ($discordHighlights === []) {
        $discordHighlights = [
            ['label' => '', 'icon' => $discordDefaultIcons[0], 'color' => $discordDefaultColors[0]],
            ['label' => '', 'icon' => $discordDefaultIcons[1], 'color' => $discordDefaultColors[1]],
            ['label' => '', 'icon' => $discordDefaultIcons[2], 'color' => $discordDefaultColors[2]],
        ];
    }

    // The repeater is capped at 3 rows, so drop anything beyond that.
    $discordHighlights = array_slice($discordHighlights, 0, 3);

    // Coerce each row to string label/icon/color and a valid six-digit hex.
    $discordHighlights = array_map(static function ($highlight, $index) use ($discordDefaultIcons, $discordDefaultColors) {
        $highlight = is_array($highlight) ? $highlight : [];
        $color = $highlight['color'] ?? $discordDefaultColors[$index] ?? $discordDefaultColors[0];

        if (! is_string($color) || preg_match('/^#[0-9a-fA-F]{6}$/', $color) !== 1) {
            $color = $discordDefaultColors[$index] ?? $discordDefaultColors[0];
        }

        return [
            'label' => is_string($highlight['label'] ?? null) ? $highlight['label'] : '',
            'icon' => is_string($highlight['icon'] ?? null) ? $highlight['icon'] : ($discordDefaultIcons[$index] ?? 'bi-star-fill'),
            'color' => strtolower($color),
        ];
    }, $discordHighlights, array_keys($discordHighlights));
@endphp

<x-admin.config.ui.tab
    id="discord"
    :title="trans('theme::admin.discord_title')"
    :description="trans('theme::admin.config_desc_discord')"
    icon="bi-discord"
    :active="$active"
>
    <div class="alert alert-info d-flex align-items-start gap-2 py-2 px-3" role="status">
        <i class="bi bi-info-circle-fill mt-1" aria-hidden="true"></i>
        <span>{{ trans('theme::admin.discord_not_customizable') }}</span>
    </div>

    <div class="card config-block mb-3">
        <div class="card-body">
            <x-admin.config.ui.switch
                name="discord.visible"
                :default="true"
                :label="trans('theme::admin.discord_visible')"
                :hint="trans('theme::admin.discord_visible_hint')"
                class="mb-3"
            />

            <div class="row g-3">
                <x-admin.config.ui.field
                    name="discord.invite"
                    :label="trans('theme::admin.discord_invite')"
                    :maxlength="32"
                    :placeholder="trans('theme::admin.discord_invite_placeholder')"
                    :hint="trans('theme::admin.discord_invite_hint')"
                    col="col-12 col-lg-6"
                />

                <x-admin.config.ui.field
                    name="discord.title"
                    :label="trans('theme::admin.discord_name')"
                    :maxlength="100"
                    :placeholder="trans('theme::admin.discord_name_placeholder')"
                    :hint="trans('theme::admin.discord_name_hint', ['max' => 100])"
                    col="col-12 col-lg-6"
                >
                    <x-slot:actions>
                        <x-admin.config.ui.switch
                            name="discord.show_title"
                            :default="true"
                            :label="trans('theme::admin.show_title')"
                            class="mb-0"
                        />
                    </x-slot:actions>
                </x-admin.config.ui.field>
            </div>
        </div>
    </div>

    <div class="card config-block">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <h3 class="h5 mb-0">{{ trans('theme::admin.discord_highlights') }}</h3>

                <span role="img" tabindex="0" aria-label="{{ trans('theme::admin.discord_highlights_hint') }}" data-bs-toggle="tooltip" title="{{ trans('theme::admin.discord_highlights_hint') }}">
                    <i class="bi bi-info-circle-fill text-primary" aria-hidden="true"></i>
                </span>

                <a class="btn btn-sm btn-outline-primary text-nowrap ms-auto" href="https://icons.getbootstrap.com/" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> {{ trans('theme::admin.discord_icon_help') }}
                </a>
            </div>

            <x-admin.config.ui.repeater
                id="discordHighlightsList"
                name="discord.highlights"
                row="sections.discord.highlight-row"
                row-class="discord-highlight-row"
                :rows="$discordHighlights"
                :min="1"
                :max="3"
                :defaults="['icons' => $discordDefaultIcons, 'colors' => $discordDefaultColors]"
                :add-label="trans('theme::admin.discord_highlight_add')"
                add-id="addDiscordHighlightButton"
                template-id="discordHighlightTemplate"
            />
        </div>
    </div>
</x-admin.config.ui.tab>
