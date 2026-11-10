{{--
    Admin config: one Discord highlight row (repeater).

    $index    Row position, or "__INDEX__" for the <template> clone.
    $value    Saved row (label, icon, color).
    $template When true, renders the blank clone source instead of real data.
--}}
@props([
    'index',
    'value' => [],
    'template' => false,
])

@php
    $isTemplate = (bool) $template;
    $index = (string) $index;
    $value = is_array($value) ? $value : [];

    $defaultIcons = ['bi-trophy-fill', 'bi-chat-dots-fill', 'bi-hash'];
    $defaultColors = ['#FBE0DC', '#E4E2FA', '#DDF3E6'];
    // Cycle the three default icon/color pairs so new rows differ.
    $rank = is_numeric($index) ? ((int) $index % 3) : 0;

    // Displayed value: old() > saved value > default. The template stays blank.
    $highlightLabel = $isTemplate ? '' : old('discord.highlights.' . $index . '.label', $value['label'] ?? '');
    $highlightIcon = $isTemplate ? $defaultIcons[0] : old('discord.highlights.' . $index . '.icon', $value['icon'] ?? $defaultIcons[$rank]);
    $highlightColor = $isTemplate ? $defaultColors[0] : old('discord.highlights.' . $index . '.color', $value['color'] ?? $defaultColors[$rank]);

    // Keep only a valid six-digit hex, lowercased for consistent display.
    $highlightColor = is_string($highlightColor) && preg_match('/^#[0-9a-fA-F]{6}$/', $highlightColor) === 1
        ? strtolower($highlightColor)
        : $defaultColors[$rank];

    // The template never carries validation errors.
    $labelError = ! $isTemplate && $errors->has('discord.highlights.' . $index . '.label');
    $iconError = ! $isTemplate && $errors->has('discord.highlights.' . $index . '.icon');
    $colorError = ! $isTemplate && $errors->has('discord.highlights.' . $index . '.color');
@endphp

<div class="row g-2 align-items-end discord-highlight-row" data-repeater-index="{{ $index }}">
    <div class="col-12 col-md-5">
        <label class="form-label m-0" for="discordHighlightLabel{{ $index }}">{{ trans('theme::admin.discord_highlight_label') }}</label>

        <input
            type="text"
            class="form-control @if($labelError) is-invalid @endif"
            id="discordHighlightLabel{{ $index }}"
            name="discord[highlights][{{ $index }}][label]"
            value="{{ $highlightLabel }}"
            maxlength="60"
            placeholder="{{ trans('theme::admin.discord_highlight_label_placeholder') }}"
            @if($labelError) aria-invalid="true" aria-describedby="discordHighlightLabel{{ $index }}-error" @endif
        >

        @if($labelError)
            <span class="invalid-feedback d-block" id="discordHighlightLabel{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('discord.highlights.' . $index . '.label') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-12 col-md-4">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <label class="form-label m-0" for="discordHighlightIcon{{ $index }}">{{ trans('theme::admin.discord_highlight_icon') }}</label>
        </div>

        <input
            type="text"
            class="form-control font-monospace @if($iconError) is-invalid @endif"
            id="discordHighlightIcon{{ $index }}"
            name="discord[highlights][{{ $index }}][icon]"
            value="{{ $highlightIcon }}"
            maxlength="64"
            pattern="bi-[a-z0-9-]{1,60}"
            placeholder="{{ trans('theme::admin.discord_highlight_icon_placeholder') }}"
            data-discord-icon
            @if($iconError) aria-invalid="true" aria-describedby="discordHighlightIcon{{ $index }}-error" @endif
        >

        @if($iconError)
            <span class="invalid-feedback d-block" id="discordHighlightIcon{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('discord.highlights.' . $index . '.icon') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-8 col-md-2">
        <label class="form-label m-0" for="discordHighlightColor{{ $index }}">{{ trans('theme::admin.discord_highlight_color') }}</label>

        <div class="input-group input-group-sm">
            <input
                type="color"
                class="form-control form-control-color p-1 @if($colorError) is-invalid @endif"
                id="discordHighlightColor{{ $index }}"
                name="discord[highlights][{{ $index }}][color]"
                value="{{ $highlightColor }}"
                data-discord-color
                data-color-hex="discordHighlightColor{{ $index }}-hex"
                @if($colorError) aria-invalid="true" aria-describedby="discordHighlightColor{{ $index }}-error" @endif
            >
            <span class="input-group-text font-monospace small" id="discordHighlightColor{{ $index }}-hex">{{ $highlightColor }}</span>
        </div>

        @if($colorError)
            <span class="invalid-feedback d-block" id="discordHighlightColor{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('discord.highlights.' . $index . '.color') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-4 col-md-1">
        <button
            type="button"
            class="btn btn-sm btn-outline-danger w-100"
            data-repeater-remove
            aria-label="{{ trans('theme::admin.discord_highlight_remove') }}"
        >
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>
