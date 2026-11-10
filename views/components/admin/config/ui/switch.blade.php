{{--
    Admin config: toggle switch (boolean field).

    Renders a Bootstrap switch bound to a theme config key. The current state
    comes from old input first (after a validation error), then from the stored
    theme config, then from $default.

    @param string      $name     Dot-notation config key (e.g. "navbar.sticky")
    @param string      $label    Visible label
    @param string|null $hint     Help text displayed under the switch
    @param bool        $default  State used when the key is not stored yet
    @param string|null $id       Custom HTML id (derived from $name when omitted)

    Extra HTML attributes are merged onto the wrapper <div>.
--}}
@props([
    'name',
    'label',
    'hint' => null,
    'default' => false,
    'id' => null,
])

@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug(str_replace('.', '-', $name));
    $htmlName = preg_replace('/\.([^.]+)/', '[$1]', $name);
    $checked = old($name, theme_config($name, $default));
    $checked = in_array($checked, [true, 1, '1', 'true', 'on'], true);

    $hasError = $errors->has($name);
    $describedBy = array_values(array_filter([
        $hint ? $fieldId . '-hint' : null,
        $hasError ? $fieldId . '-error' : null,
    ]));
@endphp

<div {{ $attributes->class(['form-check', 'form-switch']) }}>
    <input type="hidden" name="{{ $htmlName }}" value="0">

    <input
        type="checkbox"
        class="form-check-input @if($hasError) is-invalid @endif"
        id="{{ $fieldId }}"
        name="{{ $htmlName }}"
        value="1"
        @checked($checked)
        @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        @if($hasError) aria-invalid="true" @endif
    >

    <label class="form-check-label" for="{{ $fieldId }}">{{ $label }}</label>

    @if($hint)
        <small class="d-block form-text" id="{{ $fieldId }}-hint">{{ $hint }}</small>
    @endif

    @if($hasError)
        <span class="invalid-feedback d-block" id="{{ $fieldId }}-error" role="alert">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
