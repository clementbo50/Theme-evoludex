{{--
    Admin config: one footer link row (repeater).

    $index    Row position, or "__INDEX__" for the <template> clone.
    $value    Saved row (name, value, new_tab).
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

    // Displayed value: old() > saved value > default. The template stays blank.
    $linkName = $isTemplate ? '' : old('footer.links.' . $index . '.name', $value['name'] ?? '');
    $linkValue = $isTemplate ? '/' : old('footer.links.' . $index . '.value', $value['value'] ?? '/');
    $linkNewTab = $isTemplate ? false : (bool) old('footer.links.' . $index . '.new_tab', $value['new_tab'] ?? false);

    // The template never carries validation errors.
    $nameError = ! $isTemplate && $errors->has('footer.links.' . $index . '.name');
    $valueError = ! $isTemplate && $errors->has('footer.links.' . $index . '.value');
@endphp

<div class="row g-2 align-items-end footer-link-row" data-repeater-index="{{ $index }}">
    <div class="col-12 col-md-4">
        <label class="form-label m-0" for="footerLinkName{{ $index }}">{{ trans('theme::admin.footer_link_name') }}</label>

        <input
            type="text"
            class="form-control @if($nameError) is-invalid @endif"
            id="footerLinkName{{ $index }}"
            name="footer[links][{{ $index }}][name]"
            value="{{ $linkName }}"
            maxlength="60"
            @if($nameError) aria-invalid="true" aria-describedby="footerLinkName{{ $index }}-error" @endif
        >

        @if($nameError)
            <span class="invalid-feedback d-block" id="footerLinkName{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('footer.links.' . $index . '.name') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label m-0" for="footerLinkRoute{{ $index }}">{{ trans('theme::admin.footer_link_route') }}</label>

        <input
            type="text"
            class="form-control @if($valueError) is-invalid @endif"
            id="footerLinkRoute{{ $index }}"
            name="footer[links][{{ $index }}][value]"
            value="{{ $linkValue }}"
            maxlength="500"
            placeholder="/"
            @if($valueError) aria-invalid="true" aria-describedby="footerLinkRoute{{ $index }}-error" @endif
        >

        @if($valueError)
            <span class="invalid-feedback d-block" id="footerLinkRoute{{ $index }}-error" role="alert">
                <strong>{{ $errors->first('footer.links.' . $index . '.value') }}</strong>
            </span>
        @endif
    </div>

    <div class="col-8 col-md-3">
        {{-- Sent before the checkbox: an unchecked box posts no value at all. --}}
        <input type="hidden" name="footer[links][{{ $index }}][new_tab]" value="0">

        <div class="form-check">
            <input
                type="checkbox"
                class="form-check-input"
                id="footerLinkTab{{ $index }}"
                name="footer[links][{{ $index }}][new_tab]"
                value="1"
                @checked($linkNewTab)
            >
            <label class="form-check-label" for="footerLinkTab{{ $index }}">{{ trans('theme::admin.footer_link_tab') }}</label>
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
