@extends('layouts.base')

@php
    // Shared with the sections that push their own scripts.
    $themeVersion = themes()->findDescription(themes()->currentTheme())?->version ?? '1';
@endphp

@section('title', trans('messages.home'))

@section('app')

@if(theme_config('hero.visible', true))
    @include('partials.hero')
@endif
    <main id="decouvrir">
        <h1 class="visually-hidden">{{ trans('theme::theme.home_heading', ['name' => site_name(), 'game' => game()->name()]) }}</h1>
        @include('elements.session-alerts')

        @if(theme_config('about.visible', true))
            @include('partials.about')
        @endif

        @include('partials.news')

        @include('partials.stats')

        @include('partials.discord')

    </main>
@endsection
