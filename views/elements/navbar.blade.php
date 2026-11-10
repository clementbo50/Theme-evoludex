@php
    $navbarLogoSize = (int) theme_config('navbar.logo_size', 48);
    $navbarLogoSize = max(32, min(64, $navbarLogoSize));
    $navbarShowBrandText = theme_config('navbar.show_brand_text', true);
    $navbarShowDiscord = theme_config('navbar.show_discord', true);
    $navbarDiscord = social_links()->firstWhere('type', 'discord');
    $avatarUrl = Auth::user()?->getAvatar(64) ?? theme_asset('img/pokeball.svg');
@endphp
<nav class="navbar navbar-expand-lg py-3">
    <div class="poke-roof position-absolute top-0 start-0 end-0 pe-none" aria-hidden="true"></div>
    <img class="poke-sign position-absolute top-100 start-50 translate-middle rounded-circle bg-white pe-none d-none d-lg-block" src="{{ theme_asset('img/pokeball.svg') }}" alt="" aria-hidden="true" width="52" height="52" fetchpriority="high">
    <div class="container mt-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" aria-label="{{ site_name() }}">
            <img src="{{ favicon() }}" alt="" width="{{ $navbarLogoSize }}" height="{{ $navbarLogoSize }}">
            @if($navbarShowBrandText)
                <span class="navbar-brand-text">{{ site_name() }}</span>
            @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="{{ trans('messages.nav.toggle') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto text-center align-items-center gap-1">
                @foreach($navbar as $element)
                    @if(!$element->isDropdown())
                        <li class="nav-item">
                            <a class="nav-link @if($element->isCurrent()) active @endif" href="{{ $element->getLink() }}" @if($element->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $element->name }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle @if($element->isCurrent()) active @endif" href="#" id="navbarDropdown{{ $element->id }}" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $element->name }}
                            </a>
                            <div class="dropdown-menu text-center text-lg-start" aria-labelledby="navbarDropdown{{ $element->id }}">
                                @foreach($element->elements as $childElement)
                                    <a class="dropdown-item @if($childElement->isCurrent()) active @endif" href="{{ $childElement->getLink() }}" @if($childElement->new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                        {{ $childElement->name }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto text-center align-items-center gap-1">
                @if($navbarShowDiscord && $navbarDiscord)
                    <li class="nav-item">
                        <a class="nav-link poke-discord d-flex justify-content-center" href="{{ $navbarDiscord->value }}" target="_blank" rel="noopener noreferrer" aria-label="{{ trans('theme::theme.discord') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ trans('theme::theme.discord_tooltip') }}">
                            <img src="{{ theme_asset('img/discord-pokeball.svg') }}" alt="" aria-hidden="true" width="36" height="36">
                        </a>
                    </li>
                @endif

                @include('elements.theme-selector')

                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> {{ trans('auth.login') }}
                        </a>
                    </li>

                    @if(Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus-fill"></i> {{ trans('auth.register') }}
                            </a>
                        </li>
                    @endif
                @else
                    @include('elements.notifications')

                    <li class="nav-item dropdown">
                        <a id="userDropdown" class="nav-link dropdown-toggle trainer-card" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <span class="trainer-id">
                                <img class="trainer-head" src="{{ $avatarUrl }}" alt="" aria-hidden="true" width="40" height="40" loading="lazy" onerror="this.onerror=null;this.src='{{ theme_asset('img/pokeball.svg') }}';">
                            </span>
                            <span class="trainer-meta">
                                <span class="trainer-name">{{ Auth::user()->name }}</span>
                                <span class="trainer-rank d-none d-lg-inline">{{ Auth::user()->role?->name ?? trans('theme::theme.trainer') }}</span>
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end text-center text-lg-start" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="bi bi-person"></i> {{ trans('messages.nav.profile') }}
                            </a>

                            @foreach(plugins()->getUserNavItems() ?? [] as $navId => $navItem)
                                <a class="dropdown-item" href="{{ route($navItem['route']) }}">
                                    <i class="{{ $navItem['icon'] ?? 'bi bi-three-dots' }}"></i> {{ $navItem['name'] }}
                                </a>
                            @endforeach

                            @if(Auth::user()->hasAdminAccess())
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> {{ trans('messages.nav.admin') }}
                                </a>
                            @endif

                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> {{ trans('auth.logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
