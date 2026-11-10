{{--
    Generic component: one admin config field (label, control, hint, error).

    Supported controls: text-like inputs (any native type), textarea,
    select and range. The stored value is read from theme_config($name).

    @param string      $name          Dotted config key (e.g. "about.description")
    @param string      $label         Field label
    @param string      $type          Native input type, or textarea | select | range
    @param mixed       $default       Fallback when no value is stored
    @param string|null $hint          Help text displayed under the field
    @param string|null $hintUrl       External link displayed under the field
    @param string|null $hintLinkLabel Link label (required with $hintUrl)
    @param string|null $tooltip       Info tooltip next to the label
    @param string|null $placeholder   Input placeholder
    @param int|null    $maxlength     Maximum number of characters
    @param int|null    $min           Minimum value (number, range)
    @param int|null    $max           Maximum value (number, range)
    @param int|null    $step          Step (number, range)
    @param int         $rows          Number of rows (textarea only, default 3)
    @param array       $options       Choices as [value => label] (select only, default [])
    @param string      $col           Bootstrap column class of the wrapper
    @param string|null $id            Custom HTML id (derived from $name when omitted)

    <x-slot:actions>  Optional extra controls displayed at the right of the label

    Extra HTML attributes are merged onto the wrapper <div>.
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'default' => null,
    'hint' => null,
    'hintUrl' => null,
    'hintLinkLabel' => null,
    'tooltip' => null,
    'placeholder' => null,
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'rows' => 3,
    'options' => [],
    'col' => 'col-12',
    'id' => null,
])

@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug(str_replace('.', '-', $name));
    $htmlName = preg_replace('/\.([^.]+)/', '[$1]', $name);
    $fieldValue = old($name, theme_config($name, $default));
    $fieldValue = is_scalar($fieldValue) ? (string) $fieldValue : '';

    $hasError = $errors->has($name);
    $describedBy = array_values(array_filter([
        $hint ? $fieldId . '-hint' : null,
        $hasError ? $fieldId . '-error' : null,
    ]));
@endphp

<div {{ $attributes->class([$col]) }}>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
        <label class="form-label mb-0" for="{{ $fieldId }}">{{ $label }}</label>

        @if($tooltip)
            <span role="img" tabindex="0" aria-label="{{ $tooltip }}" data-bs-toggle="tooltip" title="{{ $tooltip }}">
                <i class="bi bi-info-circle-fill text-primary" aria-hidden="true"></i>
            </span>
        @endif

        @isset($actions)
            <div class="config-field-actions d-flex align-items-center ms-auto">
                {{ $actions }}
            </div>
        @endisset
    </div>

    @if($type === 'textarea')
        <textarea
            class="form-control @if($hasError) is-invalid @endif"
            id="{{ $fieldId }}"
            name="{{ $htmlName }}"
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
            @if($hasError) aria-invalid="true" @endif
        >{{ $fieldValue }}</textarea>
    @elseif($type === 'select')
        <select
            class="form-select @if($hasError) is-invalid @endif"
            id="{{ $fieldId }}"
            name="{{ $htmlName }}"
            @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
            @if($hasError) aria-invalid="true" @endif
        >
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($fieldValue === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'range')
        <div class="d-flex align-items-center gap-3">
            <input
                type="range"
                class="form-range @if($hasError) is-invalid @endif"
                id="{{ $fieldId }}"
                name="{{ $htmlName }}"
                value="{{ $fieldValue }}"
                @if($min !== null) min="{{ $min }}" @endif
                @if($max !== null) max="{{ $max }}" @endif
                @if($step !== null) step="{{ $step }}" @endif
                @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
                @if($hasError) aria-invalid="true" @endif
            >
            <output class="config-range-output text-end font-monospace" for="{{ $fieldId }}" id="{{ $fieldId }}-value">{{ $fieldValue }}</output>
        </div>
    @else
        <input
            type="{{ $type }}"
            class="form-control @if($hasError) is-invalid @endif"
            id="{{ $fieldId }}"
            name="{{ $htmlName }}"
            value="{{ $fieldValue }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step !== null) step="{{ $step }}" @endif
            @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
            @if($hasError) aria-invalid="true" @endif
        >
    @endif

    @if($hint)
        <small class="form-text d-block" id="{{ $fieldId }}-hint">{{ $hint }}</small>
    @endif

    @if($hintUrl && $hintLinkLabel)
        <a class="btn btn-sm btn-outline-primary text-nowrap mt-1" href="{{ $hintUrl }}" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> {{ $hintLinkLabel }}
        </a>
    @endif

    @if($hasError)
        <span class="invalid-feedback d-block" id="{{ $fieldId }}-error" role="alert">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
