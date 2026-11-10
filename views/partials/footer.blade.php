<footer class="mt-auto pt-5 pb-3 poke-footer-split position-relative bg-body">
    <div class="container">
          <div class="d-flex align-items-end justify-content-end mb-0">
            <div class="poke-battle-header d-flex gap-2 align-items-center flex-wrap justify-content-center overflow-hidden mw-100">
                @php
                    $pokeSocialIcons = [
                        'discord' => 'discord-pokeball.svg',
                        'youtube' => 'youtube-pokeball.svg',
                        'twitter' => 'twitter-pokeball.svg',
                        'x-twitter' => 'x-pokeball.svg',
                        'steam' => 'steam-pokeball.svg',
                        'instagram' => 'instagram-pokeball.svg',
                        'facebook' => 'facebook-pokeball.svg',
                    ];
                @endphp
                @foreach(social_links() as $link)
                    @if(theme_config('footer.social_pokeball', true) && isset($pokeSocialIcons[$link->type]))
                        <a href="{{ $link->value }}" title="{{ $link->title }}" target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" class="d-inline-block" aria-label="{{ $link->title }}">
                            <img src="{{ theme_asset('img/' . $pokeSocialIcons[$link->type]) }}" alt="" aria-hidden="true" width="48" height="48">
                        </a>
                    @else
                        <a href="{{ $link->value }}" title="{{ $link->title }}" target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fs-5" style="width: 48px; height: 48px; background: {{ $link->color }}">
                            <i class="{{ $link->icon }}"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="poke-panel position-relative p-3 p-lg-4 overflow-hidden">

        @php
            $footerDescription = theme_config('footer.description', '');
        @endphp
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <a class="d-inline-flex align-items-center gap-2" href="{{ route('home') }}" aria-label="{{ site_name() }}">
                    <img src="{{ site_logo() ?: favicon() }}" alt="{{ site_name() }}" width="120" height="120" loading="lazy">
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <h3 class="h5">{{ theme_config('footer.title') ?: trans('theme::theme.footer_name_placeholder') }}</h3>
                <p>{{ $footerDescription !== '' ? $footerDescription : trans('theme::theme.footer_desc_placeholder') }}</p>
            </div>
            <div class="col-12 col-md-6 col-lg-4 text-lg-center">
                <h3 class="h5">{{ trans('theme::theme.footer_plan_placeholder') }}</h3>
                <nav aria-label="{{ trans('theme::theme.footer_plan_placeholder') }}">
                    @php
                        $footerLinkConfig = theme_config('footer.links');
                        $footerLinks = is_array($footerLinkConfig) ? $footerLinkConfig : [];
                        $footerLinks = array_values(array_filter($footerLinks, fn ($link) => is_array($link) && !empty($link['name']) && !empty($link['value'])));
                    @endphp
                    @foreach(array_chunk($footerLinks, 2) as $footerPair)
                        <div class="mb-2">
                            <div class="btn-group" role="group">
                                @foreach($footerPair as $footerLink)
                                    @php
                                        // Rules only run on save, so a stored value is checked again before it reaches an href.
                                        $footerHrefValid = is_string($footerLink['value'])
                                            && preg_match('#^(?:https?://|/(?!/)|\#)[^\s\\\\]*$#', $footerLink['value']) === 1;
                                    @endphp
                                    @if($footerHrefValid)
                                        <a href="{{ $footerLink['value'] }}" class="btn btn-outline-secondary btn-sm" @if(!empty($footerLink['new_tab'])) target="_blank" rel="noopener noreferrer" @endif>
                                            {{ $footerLink['name'] }}
                                        </a>
                                    @else
                                        <span class="btn btn-outline-secondary btn-sm disabled" aria-disabled="true">{{ $footerLink['name'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
        </div>
        </div>
    </div>
    @php
        $footerLegal = theme_config('footer.legal', '');
    @endphp
    <div class="mt-4 pt-3">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center gap-2 gap-lg-3 text-center">
                <p class="mb-0">{{ str_replace('{year}', date('Y'), setting('copyright')) }} | @lang('messages.copyright') </p>

                @if($footerLegal !== '')
                    <p class="mb-0">{{ $footerLegal }}</p>
                @endif
            </div>
        </div>
    </div>
</footer>
