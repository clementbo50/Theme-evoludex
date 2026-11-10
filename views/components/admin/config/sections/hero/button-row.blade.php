{{--
    Admin config: one hero button row (repeater).

    $index    Row position, or "__INDEX__" for the <template> clone.
    $value    Saved row (type, label, value, new_tab).
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

    // Allowed button types (key => translated label).
    $types = [
        'copy' => trans('theme::admin.hero_type_copy'),
        'download' => trans('theme::admin.hero_type_download'),
        'link' => trans('theme::admin.hero_type_link'),
    ];

    // Displayed value: old() > saved value > default. The template stays blank.
    $buttonType = $isTemplate ? 'copy' : old('hero.buttons.' . $index . '.type', $value['type'] ?? 'copy');
    $buttonType = array_key_exists($buttonType, $types) ? $buttonType : 'copy';
    $buttonLabel = $isTemplate ? '' : old('hero.buttons.' . $index . '.label', $value['label'] ?? '');
    $buttonValue = $isTemplate ? '' : old('hero.buttons.' . $index . '.value', $value['value'] ?? '');
    $buttonNewTab = $isTemplate ? false : (bool) old('hero.buttons.' . $index . '.new_tab', $value['new_tab'] ?? false);
    // A copy button has neither a value nor a target: hide and disable both fields.
    $isCopy = $buttonType === 'copy';

    // The template never carries validation errors.
    $typeError = ! $isTemplate && $errors->has('hero.buttons.' . $index . '.type');
    $labelError = ! $isTemplate && $errors->has('hero.buttons.' . $index . '.label');
    $valueError = ! $isTemplate && $errors->has('hero.buttons.' . $index . '.value');
@endphp

<div class="row g-2 align-items-end hero-button-row" data-repeater-index="{{ $index }}">
    <div class="col-12 col-md-3">
        <label class="form-label m-0" for="heroButtonType{{ $index }}">{{ trans('theme::admin.hero_button_type') }}</label>

        <select
            class="form-select @if($typeError) is-invalid @endif"
            id="heroButtonType{{ $index }}"
            name="hero[buttons][{{ $index }}][type]"
            data-hero-type
            @if($typeError) aria-invalid="true" aria-describedby="heroButtonType{{ $index }}-error" @endif
        >
            @foreach($types as $typeValue => $typeLabel)
                <option value="{{ $typeValue }}" @selected($buttonType === $typeValue)>{{ $typeLabel }}</option>
            @endforeach
        </select>

        @if($typeError)
            <span class="invalid-feedback d-block" id="heroButtonType{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('hero.buttons.' . $index . '.type') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label m-0" for="heroButtonLabel{{ $index }}">{{ trans('theme::admin.hero_button_label') }}</label>

        <input
            type="text"
            class="form-control @if($labelError) is-invalid @endif"
            id="heroButtonLabel{{ $index }}"
            name="hero[buttons][{{ $index }}][label]"
            value="{{ $buttonLabel }}"
            maxlength="40"
            @if($labelError) aria-invalid="true" aria-describedby="heroButtonLabel{{ $index }}-error" @endif
        >

        @if($labelError)
            <span class="invalid-feedback d-block" id="heroButtonLabel{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('hero.buttons.' . $index . '.label') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-12 col-md-3 hero-button-value-col @if($isCopy) d-none @endif">
        <label class="form-label m-0" for="heroButtonValue{{ $index }}">{{ trans('theme::admin.hero_button_value') }}</label>

        <input
            type="text"
            class="form-control @if($valueError) is-invalid @endif"
            id="heroButtonValue{{ $index }}"
            name="hero[buttons][{{ $index }}][value]"
            value="{{ $buttonValue }}"
            maxlength="500"
            placeholder="{{ trans('theme::admin.hero_button_value_placeholder') }}"
            @if($isCopy) disabled @endif
            @if($valueError) aria-invalid="true" aria-describedby="heroButtonValue{{ $index }}-error" @endif
        >

        @if($valueError)
            <span class="invalid-feedback d-block" id="heroButtonValue{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('hero.buttons.' . $index . '.value') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-8 col-md-2 hero-button-tab-col @if($isCopy) d-none @endif">
        {{-- Sent before the checkbox: an unchecked box posts no value at all. --}}
        <input type="hidden" name="hero[buttons][{{ $index }}][new_tab]" value="0" @if($isCopy) disabled @endif>

        <div class="form-check">
            <input
                type="checkbox"
                class="form-check-input"
                id="heroButtonTab{{ $index }}"
                name="hero[buttons][{{ $index }}][new_tab]"
                value="1"
                @checked($buttonNewTab)
                @if($isCopy) disabled @endif
            >
            <label class="form-check-label" for="heroButtonTab{{ $index }}">{{ trans('theme::admin.hero_button_tab') }}</label>
        </div>
    </div>

    <div class="col-4 col-md-1">
        <button
            type="button"
            class="btn btn-sm btn-outline-danger w-100"
            data-repeater-remove
            aria-label="{{ trans('messages.actions.delete') }}"
        >
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>
