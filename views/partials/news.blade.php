@php
    $newsVisible = theme_config('news.visible', true);
    $newsTitle = theme_config('news.title') ?: trans('theme::theme.news_title_default');
    $newsDescription = theme_config('news.description') ?: trans('theme::theme.news_desc_default');
    $newsShowTitle = theme_config('news.show_title', true);
    $newsShowDescription = theme_config('news.show_description', true);

    $newsPosts = ($posts ?? collect())
        ->sortByDesc('is_pinned')
        ->take(4)
        ->values();

    [$newsHero, $newsTopOne, $newsTopTwo, $newsWide] = $newsPosts->pad(4, null)->all();
@endphp
@if($newsVisible)
<section class="poke-split poke-split--reverse d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center min-vh-100" aria-label="{{ $newsTitle }}">
    <div class="container py-5 my-auto my-lg-0">
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <p class="text-uppercase fw-bold small mb-2"> {{ trans('theme::theme.news_eyebrow') }}</p>
                <h2 id="news-title" @class(['mb-2', 'text-break', 'visually-hidden' => ! $newsShowTitle])>{{ $newsTitle }}</h2>
                @if($newsShowDescription)
                    <p class="fs-5">{{ $newsDescription }}</p>
                @endif
            </div>
            <div class="col-12 col-lg-6 text-lg-end poke-split-right">
                <a href="{{ route('posts.index') }}" class="btn btn-primary">{{ trans('theme::theme.news_all') }}</a>
            </div>
        </div>
        @if($newsPosts->isEmpty())
        <div class="col-12 col-lg-6">
            <div class="alert">{{ trans('theme::theme.news_empty') }}</div>
        </div>
        @else
            <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-6">
                    @include('partials.news-card', ['cardPost' => $newsHero, 'cardEager' => true, 'cardHeight' => 'clamp(240px, 60vw, 420px)'])
                </div>
                <div class="col-12 col-lg-6 poke-split-right">
                    <div class="d-flex flex-column gap-4 h-100">
                        <div class="row g-4">
                            <div class="col-12 col-sm-6">
                                @include('partials.news-card', ['cardPost' => $newsTopOne, 'cardHeight' => '150px'])
                            </div>
                            <div class="col-12 col-sm-6">
                                @include('partials.news-card', ['cardPost' => $newsTopTwo, 'cardHeight' => '150px'])
                            </div>
                        </div>
                        @include('partials.news-card', ['cardPost' => $newsWide, 'cardHeight' => '180px'])
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="poke-split-decor" aria-hidden="true"></div>
</section>
@endif
