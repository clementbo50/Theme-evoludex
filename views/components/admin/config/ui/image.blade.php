{{--
    Admin config: image selector with live preview.

    Renders a <select> listing the images of the library, plus a preview below.
    The stored value is a library file name, resolved with image_url(); an
    empty value shows no selection and no preview.

    The preview <img> is updated by the page JavaScript through the
    data-image-preview and data-base attributes.

    @param string        $name     Dot-notation config key (e.g. "about.image")
    @param string        $label    Visible label
    @param iterable      $images   Library images, each exposing ->file and ->name
    @param string|null   $default  Value used when nothing is stored yet
    @param string|null   $hint     Help text displayed under the select
    @param string        $col      Bootstrap grid classes for the wrapper
    @param string|null   $id       Custom HTML id (derived from $name when omitted)

    Extra attributes are merged onto the wrapper <div>.
--}}
@props([
    'name',
    'label',
    'images' => [],
    'default' => null,
    'hint' => null,
    'col' => 'col-12',
    'id' => null,
])

@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug(str_replace('.', '-', $name));
    $htmlName = preg_replace('/\.([^.]+)/', '[$1]', $name);
    $rawValue = old($name, theme_config($name, $default));
    $fieldValue = is_string($rawValue) ? $rawValue : '';

    // Defensive: tolerate null or non-iterable input.
    $images = is_iterable($images) ? $images : [];

    $previewId = $fieldId . '-preview';
    $previewUrl = $fieldValue !== '' ? image_url($fieldValue) : '';

    $hasError = $errors->has($name);
    $describedBy = array_values(array_filter([
        $hint ? $fieldId . '-hint' : null,
        $hasError ? $fieldId . '-error' : null,
    ]));
@endphp

<div {{ $attributes->class([$col]) }}>
    <label class="form-label mb-1" for="{{ $fieldId }}">{{ $label }}</label>

    {{--
        data-base:          base URL used by JS to build the preview URL from a file name.
        data-image-preview: id of the <img> to update when the selection changes.
    --}}
    <select
        class="form-select @if($hasError) is-invalid @endif"
        id="{{ $fieldId }}"
        name="{{ $htmlName }}"
        data-base="{{ image_url() }}"
        data-image-preview="{{ $previewId }}"
        @if($describedBy !== []) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        @if($hasError) aria-invalid="true" @endif
    >
        <option value="">--</option>

        @foreach($images as $image)
            <option value="{{ $image->file }}" @selected($fieldValue === $image->file)>{{ $image->name }}</option>
        @endforeach
    </select>

    <div class="d-flex flex-column align-items-start gap-2">
        @if($hint)
            <small class="form-text" id="{{ $fieldId }}-hint">{{ $hint }}</small>
        @endif

        <a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('admin.images.index') }}" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-images" aria-hidden="true"></i> {{ trans('theme::admin.images_library') }}
        </a>
    </div>

    @if($hasError)
        <span class="invalid-feedback d-block" id="{{ $fieldId }}-error" role="alert">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
    {{-- Preview wrapper: hidden when empty, toggled by JS. alt="" because the image is decorative (the select already names the file). --}}
    <div class="mt-2 @if(! $previewUrl) d-none @endif" data-image-preview-wrap="{{ $previewId }}">
        <img id="{{ $previewId }}" class="img-fluid rounded theme-image-preview" src="{{ $previewUrl }}" alt="">
    </div>
</div>
