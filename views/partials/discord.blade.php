@php
    $discordVisible = theme_config('discord.visible', true);

    $discordTitle = theme_config('discord.title') ?: trans('theme::theme.discord_title_default');
    $discordTitle = mb_substr((string) $discordTitle, 0, 100);
    $discordShowTitle = theme_config('discord.show_title', true);

    $discordInvite = (string) theme_config('discord.invite', '');
    $discordInviteValid = preg_match('/^[A-Za-z0-9-]{2,32}$/', $discordInvite) === 1;

    $discordDefaultHighlights = [
        ['icon' => 'bi-trophy-fill', 'color' => '#FBE0DC'],
        ['icon' => 'bi-chat-dots-fill', 'color' => '#E4E2FA'],
        ['icon' => 'bi-hash', 'color' => '#DDF3E6'],
    ];

    $discordStoredHighlights = theme_config('discord.highlights');
    $discordStoredHighlights = is_array($discordStoredHighlights) ? array_values($discordStoredHighlights) : [];
    $discordHighlightRows = $discordStoredHighlights === []
        ? $discordDefaultHighlights
        : array_slice($discordStoredHighlights, 0, 3);

    $discordHighlights = [];

    foreach ($discordHighlightRows as $discordIndex => $discordRow) {
        $discordRow = is_array($discordRow) ? $discordRow : [];
        $discordFallback = $discordDefaultHighlights[$discordIndex] ?? $discordDefaultHighlights[0];

        $discordLabel = $discordRow['label'] ?? null;
        $discordLabel = is_string($discordLabel) ? trim($discordLabel) : '';
        $discordLabel = $discordLabel === ''
            ? trans('theme::theme.discord_highlight_'.($discordIndex + 1))
            : $discordLabel;
        $discordLabel = mb_substr((string) $discordLabel, 0, 60);

        $discordIcon = $discordRow['icon'] ?? null;
        $discordIcon = is_string($discordIcon) && preg_match('/^bi-[a-z0-9-]{1,60}$/', $discordIcon) === 1
            ? $discordIcon
            : 'bi-star-fill';

        $discordColor = $discordRow['color'] ?? null;
        $discordColor = is_string($discordColor) && preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $discordColor) === 1
            ? $discordColor
            : $discordFallback['color'];

        if (strlen($discordColor) === 4) {
            $discordColor = '#'.$discordColor[1].$discordColor[1].$discordColor[2].$discordColor[2].$discordColor[3].$discordColor[3];
        }

        $discordHighlights[] = [
            'label' => $discordLabel,
            'icon' => $discordIcon,
            'color' => $discordColor,
            'ink' => color_contrast($discordColor),
        ];
    }
@endphp

@if($discordVisible)
<section class="poke-split poke-split--reverse d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center min-vh-100" role="region" aria-labelledby="discord-title">
    <div class="container py-5 my-auto my-lg-0">
        <div class="row g-0 gap-4 justify-content-center justify-content-lg-between align-items-center">
            <div class="col-12 col-lg-6">
                <p class="text-uppercase fw-bold small mb-2">{{ trans('theme::theme.discord_eyebrow') }}</p>
                <h2 id="discord-title" @class(['fs-2', 'fw-bold', 'lh-sm', 'mb-0', 'text-break', 'visually-hidden' => ! $discordShowTitle])>{{ $discordTitle }}</h2>

                <ul class="d-flex flex-column list-unstyled mt-4 mb-0 gap-3" aria-label="{{ trans('theme::theme.discord_highlights_label') }}">
                    @foreach($discordHighlights as $discordHighlight)
                        <li>
                            <div class="poke-discord-highlight text-body border rounded-pill p-3 d-flex align-items-center gap-3">
                                <span class="poke-discord-highlight-icon d-inline-flex align-items-center justify-content-center rounded-5"
                                      style="--poke-discord-highlight-bg: {{ $discordHighlight['color'] }}; --poke-discord-highlight-ink: {{ $discordHighlight['ink'] }};"
                                      aria-hidden="true">
                                    <i class="bi {{ $discordHighlight['icon'] }}"></i>
                                </span>
                                <span class="fs-6 text-break">{{ $discordHighlight['label'] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-12 col-lg-4">
            <article class="poke-discord-card d-flex flex-column w-100"
                     data-discord
                     data-invite="{{ $discordInvite }}"
                     data-fallback="{{ trans('theme::theme.discord_fallback') }}"
                     data-badge-checking="{{ trans('theme::theme.discord_badge_checking') }}"
                     data-badge-online="{{ trans('theme::theme.discord_badge_online') }}"
                     data-badge-offline="{{ trans('theme::theme.discord_badge_offline') }}"
                     data-badge-unavailable="{{ trans('theme::theme.discord_badge_unavailable') }}"
                     data-detail-checking="{{ trans('theme::theme.discord_badge_checking_detail') }}"
                     data-detail-online="{{ trans('theme::theme.discord_badge_online_detail') }}"
                     data-detail-offline="{{ trans('theme::theme.discord_badge_offline_detail') }}"
                     data-detail-unavailable="{{ trans('theme::theme.discord_badge_unavailable_detail') }}"
                     data-detail-expired="{{ trans('theme::theme.discord_badge_expired_detail') }}"
                     data-detail-unknown="{{ trans('theme::theme.discord_badge_unknown_detail') }}"
                     data-label-members="{{ trans('theme::theme.discord_members_unavailable') }}"
                     data-label-online="{{ trans('theme::theme.discord_online_unavailable') }}"
                     data-count-members="{{ trans('theme::theme.discord_members_value') }}"
                     data-count-online="{{ trans('theme::theme.discord_online_value') }}"
                     aria-labelledby="discord-card-title">
                <header class="poke-discord-card-header d-flex align-items-center gap-3">
                    <img src="{{ theme_asset('img/pokeball-faint.svg') }}" alt="" class="poke-discord-watermark position-absolute top-50 end-0 translate-middle-y me-3 z-0 pe-none" width="88" height="88" loading="lazy" aria-hidden="true">

                    <span class="poke-discord-avatar d-inline-flex align-items-center justify-content-center rounded-circle" data-discord-avatar aria-hidden="true">
                        <i class="bi bi-discord"></i>
                    </span>

                    <div class="poke-discord-identity flex-grow-1">
                        <h3 id="discord-card-title" class="fs-5 fw-bold lh-sm mb-0 text-break">{{ site_name() }}</h3>
                        <p class="small mt-1 mb-0 text-break" data-discord-server hidden></p>
                    </div>

                    <span class="badge text-bg-secondary position-relative z-1" data-discord-badge data-state="unavailable" role="status" aria-live="polite">
                        <span data-discord-badge-text>{{ trans('theme::theme.discord_badge_unavailable') }}</span>
                        <span class="visually-hidden" data-discord-badge-detail>{{ trans('theme::theme.discord_badge_unavailable_detail') }}</span>
                    </span>
                </header>

                <div class="poke-discord-card-body d-flex flex-column flex-grow-1">
                    <div class="row g-0 border-bottom pb-3">
                        <div class="col-6 d-flex flex-column gap-1">
                            <span class="fs-4 fw-bold lh-1 text-break" aria-hidden="true" data-discord-members-value>{{ trans('theme::theme.discord_fallback') }}</span>
                            <span class="small text-uppercase fw-bold text-body-secondary" aria-hidden="true">{{ trans('theme::theme.discord_members') }}</span>
                            <span class="visually-hidden" aria-live="polite" aria-atomic="true" data-discord-members-label>{{ trans('theme::theme.discord_members_unavailable') }}</span>
                        </div>
                        <div class="col-6 d-flex flex-column gap-1 ps-3 border-start">
                            <span class="fs-4 fw-bold lh-1 text-break" aria-hidden="true" data-discord-online-value>{{ trans('theme::theme.discord_fallback') }}</span>
                            <span class="small text-uppercase fw-bold text-body-secondary" aria-hidden="true">{{ trans('theme::theme.discord_online') }}</span>
                            <span class="visually-hidden" aria-live="polite" aria-atomic="true" data-discord-online-label>{{ trans('theme::theme.discord_online_unavailable') }}</span>
                        </div>
                    </div>

                    <span class="small text-uppercase fw-bold text-body-secondary d-block mt-3" data-discord-traits-label>{{ trans('theme::theme.discord_traits') }}</span>

                    <div class="d-flex flex-wrap gap-2 mt-2 mb-4">
                        <span class="badge bg-discord" data-discord-traits-fallback>{{ trans('theme::theme.discord_fallback') }}</span>
                        <ul class="d-flex flex-wrap gap-2 list-unstyled mb-0 d-none" data-discord-traits aria-live="polite" aria-label="{{ trans('theme::theme.discord_traits_label') }}"></ul>
                    </div>

                    @if($discordInviteValid)
                        <a class="poke-discord-cta d-inline-flex align-items-center justify-content-center gap-2 w-100 mt-auto" data-discord-cta href="https://discord.gg/{{ $discordInvite }}" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-discord" aria-hidden="true"></i>
                            <span>{{ trans('theme::theme.discord_join') }}</span>
                        </a>
                    @else
                        <button type="button" class="poke-discord-cta d-inline-flex align-items-center justify-content-center gap-2 w-100 mt-auto is-disabled" data-discord-cta disabled aria-describedby="discord-cta-help">
                            <i class="bi bi-discord" aria-hidden="true"></i>
                            <span>{{ trans('theme::theme.discord_join') }}</span>
                        </button>
                    @endif

                    <span id="discord-cta-help" class="visually-hidden">{{ trans('theme::theme.discord_cta_help') }}</span>
                </div>
            </article>
            </div>
        </div>
    </div>
    <div class="poke-split-decor" aria-hidden="true"></div>
</section>

    @pushOnce('footer-scripts', 'evoludex-discord')
        <script src="{{ theme_asset('js/discord.js') }}&amp;ver={{ $themeVersion ?? '1' }}" defer></script>
    @endPushOnce
@endif
