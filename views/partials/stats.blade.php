@use('Azuriom\Models\User')

@php
    $statsVisible = theme_config('stats.visible', true);
    $statsTitle = theme_config('stats.title') ?: trans('theme::theme.stats_title_default');
    $statsDescription = theme_config('stats.description') ?: trans('theme::theme.stats_desc_default');
    $statsShowTitle = theme_config('stats.show_title', true);
    $statsShowDescription = theme_config('stats.show_description', true);

    $statsIconDefaults = [
        'players' => '#FBE0DC',
        'members' => '#E4E2FA',
        'discord' => '#DDF3E6',
    ];

    $statsIconStyles = [];

    foreach ($statsIconDefaults as $statsIconKey => $statsIconDefault) {
        $statsIconBackground = theme_config('stats.icon_backgrounds.'.$statsIconKey, $statsIconDefault);
        $statsIconBackground = is_string($statsIconBackground) && preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $statsIconBackground) === 1
            ? $statsIconBackground
            : $statsIconDefault;

        if (strlen($statsIconBackground) === 4) {
            $statsIconBackground = '#'.$statsIconBackground[1].$statsIconBackground[1].$statsIconBackground[2].$statsIconBackground[2].$statsIconBackground[3].$statsIconBackground[3];
        }

        $statsIconStyles[$statsIconKey] = [
            'background' => $statsIconBackground,
            'ink' => color_contrast($statsIconBackground),
        ];
    }

    $statsDiscordMode = theme_config('stats.discord_mode') === 'active' ? 'active' : 'registered';
    $statsDiscordInvite = (string) theme_config('discord.invite', '');
    $statsDiscordValid = preg_match('/^[A-Za-z0-9-]{2,32}$/', $statsDiscordInvite) === 1;
    $statsDiscordTitle = $statsDiscordMode === 'active'
        ? trans('theme::theme.stats_discord_active_title')
        : trans('theme::theme.stats_discord_registered_title');
    $statsDiscordValueLabel = $statsDiscordMode === 'active'
        ? trans('theme::theme.stats_discord_active_value')
        : trans('theme::theme.stats_discord_registered_value');

    $statsLocale = app()->getLocale();
    $statsThousandsSeparator = str_starts_with(strtolower($statsLocale), 'en') ? ',' : ' ';
    $statsFormatNumber = static fn (int $value): string => number_format($value, 0, '.', $statsThousandsSeparator);

    $statsServerValue = null;
    $statsServerState = 'unavailable';

    if (! empty($server ?? null)) {
        try {
            if ($server->isOnline()) {
                $statsServerValue = $server->getOnlinePlayers();
                $statsServerState = 'online';
            } else {
                $statsServerState = 'offline';
            }
        } catch (\Throwable) {
            $statsServerState = 'unavailable';
        }
    } else {
        $statsServerState = 'missing';
    }

    $statsMembers = null;

    if ($statsVisible) {
        try {
            $statsMembers = User::registered()->count();
        } catch (\Throwable) {
            $statsMembers = null;
        }
    }
@endphp

@if($statsVisible)
<section class="poke-split d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center min-vh-100" role="region" aria-labelledby="stats-title">
    <div class="container py-5 my-auto my-lg-0">
        <div class="row text-left mb-4">
            <div class="col-12 col-lg-6">
                <p class="text-uppercase fw-bold small mb-2"> {{ trans('theme::theme.stats_eyebrow') }}</p>
                <h2 id="stats-title" @class(['mb-2', 'text-break', 'visually-hidden' => ! $statsShowTitle])>{{ $statsTitle }}</h2>
                @if($statsShowDescription)
                    <p class="fs-5">{{ $statsDescription }}</p>
                @endif
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-md">
                <article class="card h-100">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2 py-4">
                        <span class="poke-stats-icon d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="--poke-stats-icon-bg: {{ $statsIconStyles['players']['background'] }}; --poke-stats-icon-ink: {{ $statsIconStyles['players']['ink'] }};"
                              aria-hidden="true">
                            <i class="bi bi-controller"></i>
                        </span>
                        <h3 class="h5 mb-0">{{ trans('theme::theme.stats_players_title') }}</h3>

                        @if($statsServerState === 'online' && $statsServerValue !== null)
                            <p class="poke-stats-value mb-0">
                                <span aria-hidden="true">{{ $statsFormatNumber($statsServerValue) }}</span>
                                <span class="visually-hidden">{{ trans('theme::theme.stats_players_value', ['count' => $statsFormatNumber($statsServerValue)]) }}</span>
                            </p>
                        @elseif($statsServerState === 'offline')
                            <p class="poke-stats-value mb-0">{{ trans('theme::theme.stats_offline') }}</p>
                        @else
                            <p class="poke-stats-value mb-0">{{ trans('theme::theme.stats_unavailable') }}</p>
                        @endif
                    </div>
                </article>
            </div>

            <div class="col-12 col-md">
                <article class="card h-100">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2 py-4">
                        <span class="poke-stats-icon d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="--poke-stats-icon-bg: {{ $statsIconStyles['members']['background'] }}; --poke-stats-icon-ink: {{ $statsIconStyles['members']['ink'] }};"
                              aria-hidden="true">
                            <i class="bi bi-person-check-fill"></i>
                        </span>
                        <h3 class="h5 mb-0">{{ trans('theme::theme.stats_members_title') }}</h3>

                        @if($statsMembers !== null)
                            <p class="poke-stats-value mb-0">
                                <span aria-hidden="true">{{ $statsFormatNumber($statsMembers) }}</span>
                                <span class="visually-hidden">{{ trans('theme::theme.stats_members_value', ['count' => $statsFormatNumber($statsMembers)]) }}</span>
                            </p>
                        @else
                            <p class="poke-stats-value mb-0">{{ trans('theme::theme.stats_unavailable') }}</p>
                        @endif
                    </div>
                </article>
            </div>

            @if($statsDiscordValid)
                <div class="col-12 col-md">
                    <article class="card h-100"
                             data-stats-discord
                             data-mode="{{ $statsDiscordMode }}"
                             data-invite="{{ $statsDiscordInvite }}"
                             data-label-success="{{ $statsDiscordValueLabel }}"
                             data-label-unavailable="{{ trans('theme::theme.stats_discord_unavailable') }}"
                             data-label-expired="{{ trans('theme::theme.stats_discord_expired') }}"
                             data-label-unknown="{{ trans('theme::theme.stats_discord_unknown') }}"
                             data-fallback="{{ trans('theme::theme.stats_discord_fallback') }}">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center gap-2 py-4">
                            <span class="poke-stats-icon d-inline-flex align-items-center justify-content-center rounded-circle"
                                  style="--poke-stats-icon-bg: {{ $statsIconStyles['discord']['background'] }}; --poke-stats-icon-ink: {{ $statsIconStyles['discord']['ink'] }};"
                                  aria-hidden="true">
                                <i class="bi bi-discord"></i>
                            </span>
                            <h3 class="h5 mb-0">{{ $statsDiscordTitle }}</h3>
                            <p class="poke-stats-value mb-0" aria-live="polite" aria-atomic="true">
                                <span aria-hidden="true" data-stats-discord-value>{{ trans('theme::theme.stats_discord_fallback') }}</span>
                                <span class="visually-hidden" data-stats-discord-label>{{ trans('theme::theme.stats_discord_unavailable') }}</span>
                            </p>
                        </div>
                    </article>

                    @pushOnce('footer-scripts', 'evoludex-discord')
                        <script src="{{ theme_asset('js/discord.js') }}&amp;ver={{ $themeVersion ?? '1' }}" defer></script>
                    @endPushOnce
                </div>
            @endif
        </div>
    </div>
    <div class="poke-split-decor" aria-hidden="true"></div>
</section>
@endif
