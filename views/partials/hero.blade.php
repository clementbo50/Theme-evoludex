@php
    $heroBg = theme_config('hero.bg');
    $heroBgUrl = $heroBg ? image_url($heroBg) : theme_asset('img/image-placeholder.svg');

    $rawButtons = theme_config('hero.buttons');

    if (! is_array($rawButtons)) {
        $rawButtons = [];
    }

    $validButtons = [];

    foreach ($rawButtons as $button) {
        if (! is_array($button)) {
            continue;
        }

        $type = $button['type'] ?? '';
        $label = $button['label'] ?? '';
        $value = $button['value'] ?? '';

        if (!in_array($type, ['copy', 'download', 'link'], true)) {
            continue;
        }

        if (empty($label)) {
            continue;
        }

        if ($type !== 'copy' && empty($value)) {
            continue;
        }

        $validButtons[] = $button;
    }
    $heroButtons = array_slice($validButtons, 0, 4);
@endphp
<section class="poke-hero d-flex align-items-center position-relative min-vh-100 overflow-hidden" style="background-image: url('{{ $heroBgUrl }}')" aria-label="{{ site_name() }}">
    <div class="poke-hero-veil position-absolute top-0 start-0 end-0 bottom-0 pe-none" aria-hidden="true"></div>
    <div class="container position-relative py-5 text-center">
        <img src="{{ site_logo() }}" alt="{{ site_name() }}" class="img-fluid" width="230" height="230" fetchpriority="high">
        <p class="mt-3 mb-0">
            @if(!empty($server ?? null))
                @if($server->isOnline())
                <span class="badge fs-6 text-wrap">
                    <i class="bi bi-circle-fill text-success" aria-hidden="true"></i>
                    {{ trans('theme::theme.hero_online', ['count' => $server->getOnlinePlayers()]) }}
                </span>
                @else
                <span class="badge fs-6 text-wrap">
                    <i class="bi bi-circle-fill text-danger" aria-hidden="true"></i>
                    {{ trans('theme::theme.hero_offline') }}
                </span>
                @endif
            @else
            <span class="badge fs-6 text-wrap">
                <i class="bi bi-circle-fill text-secondary" aria-hidden="true"></i>
                {{ trans('theme::theme.hero_no_server') }}
            </span>
            @endif
        </p>
        @if(theme_config('hero.show_description', true))
            <p class="fs-3 mt-3">{{ theme_config('hero.description') ?: trans('theme::theme.hero_desc_invite') }}</p>
        @endif
        @if(!empty($heroButtons))
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                @foreach($heroButtons as $heroButton)
                    @php
                        $heroType = $heroButton['type'] ?? '';
                        // Rules only run on save, so a stored value is checked again before it reaches an href.
                        // Copy buttons carry no href, so their value is never checked.
                        $heroHrefValid = $heroType === 'copy'
                            || (is_string($heroButton['value'] ?? null)
                                && preg_match('#^(?:https?://|/(?!/))[^\s\\\\]*$#', $heroButton['value']) === 1);
                    @endphp
                    @if($heroType === 'copy')
                        <button type="button" class="btn btn-primary poke-copy" data-copy="{{ $heroButton['label'] }}" data-copied="{{ trans('theme::theme.hero_copied') }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ trans('theme::theme.hero_copy_tip') }}" aria-live="polite">{{ $heroButton['label'] }}</button>
                    @elseif(! $heroHrefValid)
                        <span class="btn btn-secondary disabled" aria-disabled="true">{{ $heroButton['label'] }}</span>
                    @elseif($heroType === 'download')
                        <a class="btn btn-secondary" href="{{ $heroButton['value'] }}" download @if(!empty($heroButton['new_tab'])) target="_blank" rel="noopener noreferrer" @endif>{{ $heroButton['label'] }}</a>
                    @else
                        <a class="btn btn-secondary" href="{{ $heroButton['value'] }}" @if(!empty($heroButton['new_tab'])) target="_blank" rel="noopener noreferrer" @endif>{{ $heroButton['label'] }}</a>
                    @endif
                @endforeach
            </div>
        @endif
        <a href="#decouvrir" class="btn btn-outline-secondary btn-sm d-inline-block mt-5 text-decoration-none">{{ trans('theme::theme.hero_scroll') }} <i class="bi bi-chevron-down" aria-hidden="true"></i></a>
    </div>
</section>
@push('footer-scripts')
    <script src="{{ theme_asset('js/hero-copy.js') }}&amp;ver={{ $themeVersion ?? '1' }}" defer></script>
@endpush
