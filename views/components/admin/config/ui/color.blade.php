{{--
    Admin config: color picker field.

    Renders a native color input with a live hex readout. The value is read
    from old input first (after a validation error), then from the stored theme
    config, then from $default. It is normalized to a lowercase 6-digit hex so
    the browser's <input type="color"> always receives a valid value.

    The hex readout is kept in sync by the page JavaScript, through the
    data-color-hex attribute (see syncColor in the config script).

    @param string      $name     Dot-notation config key (e.g. "colors.primary")
    @param string      $label    Visible label
    @param string      $default  Fallback color, must be a valid #rrggbb value
    @param string      $col      Bootstrap grid classes for the wrapper
    @param string|null $id       Custom HTML id (derived from $name when omitted)

    Extra attributes are merged onto the wrapper <div>.
--}}
@props([
    'name',
    'label',
    'default' => '#000000',
    'col' => 'col-6 col-md-3',
    'id' => null,
])

@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug(str_replace('.', '-', $name));
    $htmlName = preg_replace('/\.([^.]+)/', '[$1]', $name);

    $rawValue = old($name, theme_config($name, $default));
    $rawValue = is_string($rawValue) ? trim($rawValue) : $default;

     // Expand shorthand hex: "#abc" -> "#aabbcc".
    if (preg_match('/^#[0-9a-fA-F]{3}$/', $rawValue) === 1) {
        $rawValue = '#' . $rawValue[1] . $rawValue[1] . $rawValue[2] . $rawValue[2] . $rawValue[3] . $rawValue[3];
    }
    // Reject anything that is not a valid #rrggbb: <input type="color"> requires this format.
    if (preg_match('/^#[0-9a-fA-F]{6}$/', $rawValue) !== 1) {
        $rawValue = $default;
    }

    $fieldValue = strtolower($rawValue);
    $hasError = $errors->has($name);
    // Accessibility: link the input to its error message when there is one.
    $describedBy = $hasError ? $fieldId . '-error' : null;
@endphp

<div {{ $attributes->class([$col]) }}>
    <label class="form-label fw-semibold mb-1" for="{{ $fieldId }}">{{ $label }}</label>

    <div class="input-group input-group-sm">
        <input
            type="color"
            class="form-control form-control-color p-1 @if($hasError) is-invalid @endif"
            id="{{ $fieldId }}"
            name="{{ $htmlName }}"
            value="{{ $fieldValue }}"
            data-color-hex="{{ $fieldId }}-hex"
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if($hasError) aria-invalid="true" @endif
        >

        <span class="input-group-text font-monospace" id="{{ $fieldId }}-hex">{{ $fieldValue }}</span>
    </div>

    @if($hasError)
        <span class="invalid-feedback d-block" id="{{ $fieldId }}-error" role="alert">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
