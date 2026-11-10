{{--
    Generic component: list of repeatable rows (add / remove handled by JS).

    Row rendering is delegated to a Blade component (the "row" prop).
    Dynamic behavior is handled by the repeater script, configured
    through the data-repeater-* attributes.

    @param string      $id         HTML id of the container
    @param string      $name       Field key used for validation (e.g. "hero.buttons")
    @param string      $row        Row component name (admin.config.{row})
    @param string|null $rowClass   Row CSS class when different from $row
    @param int         $min        Minimum number of rows
    @param int|null    $max        Maximum number of rows (null = unlimited)
    @param string      $addLabel   Add button label
    @param string      $addId      HTML id of the add button
    @param string      $templateId HTML id of the blank row <template>
    @param array       $rows       Existing rows
    @param array|null  $defaults   Default values for a new row (JSON for the JS)

    Extra HTML attributes are merged onto the wrapper <div>.
--}}

@props([
    'id',
    'name',
    'row',
    'rowClass' => null,
    'min' => 1,
    'max' => null,
    'addLabel',
    'addId',
    'templateId',
    'rows' => [],
    'defaults' => null,
])

@php
    // Reindex rows (0, 1, 2...) and ignore any non-array value
    $rows = is_array($rows) ? array_values($rows) : [];
    $min = (int) $min;
    $max = $max !== null ? (int) $max : null;
    // CSS selector of the rows, read by the JS (data-repeater-row)
    $rowSelector = '.' . ($rowClass ?? $row);
@endphp

{{-- Container: JS configuration is passed through data-repeater-* attributes --}}
<div
    id="{{ $id }}"
    {{ $attributes->class(['config-repeater', 'd-flex', 'flex-column', 'gap-2']) }}
    data-repeater
    data-repeater-row="{{ $rowSelector }}"
    data-repeater-min="{{ $min }}"
    @if($max !== null) data-repeater-max="{{ $max }}" @endif
    data-repeater-template="{{ $templateId }}"
    data-repeater-name="{{ $name }}"
    data-repeater-add="{{ $addId }}"
    @if($defaults !== null) data-repeater-defaults='@json($defaults)' @endif
>

{{-- Existing rows, rendered by the component designated by "row" --}}
   @foreach($rows as $index => $rowValue)
        <x-dynamic-component :component="'admin.config.' . $row" :index="$index" :value="$rowValue" />
    @endforeach
</div>

{{-- Blank row template: cloned by the JS, "__INDEX__" is replaced by the real index --}}
<template id="{{ $templateId }}">
    <x-dynamic-component :component="'admin.config.' . $row" index="__INDEX__" :value="[]" template />
</template>

{{-- Global field error (e.g. invalid number of rows) --}}
@error($name)
    <span class="text-danger small d-block" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror

{{-- Add button: disabled at render time if the maximum is already reached --}}
<div class="mt-2">
    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        id="{{ $addId }}"
        @if($max !== null && count($rows) >= $max) disabled @endif
    >
        <i class="bi bi-plus-lg" aria-hidden="true"></i> {{ $addLabel }}
    </button>
</div>
