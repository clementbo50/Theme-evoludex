@use('Illuminate\Support\Str')

@php
    $cardPost = $cardPost ?? null;
    $cardEager = $cardEager ?? false;
    $cardHeight = $cardHeight ?? null;
    $cardExcerptLength = $cardEager ? 320 : 140;
    $cardShowAuthor = theme_config('news.show_author', true);
    $cardShowDate = theme_config('news.show_date', true);

    $cardImage = $cardPost?->hasImage()
        ? $cardPost->imageUrl()
        : theme_asset('img/news-placeholder.svg');

    $cardTitle = $cardPost?->title ?? trans('theme::theme.news_empty_card');
    $cardAuthor = $cardShowAuthor ? $cardPost?->author?->name : null;
    $cardAvatar = $cardShowAuthor ? $cardPost?->author?->getAvatar() : null;
    $cardDate = $cardShowDate ? $cardPost?->published_at?->isoFormat('L') : null;
    $cardDateIso = $cardShowDate ? $cardPost?->published_at?->toDateString() : null;
    $cardExcerpt = $cardPost
        ? Str::limit(html_entity_decode(strip_tags($cardPost->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'), $cardExcerptLength)
        : null;
@endphp

<article class="card h-100 poke-news-card" style="max-height: 720px;">
    <img src="{{ $cardImage }}"
         alt=""
         class="w-100 object-fit-cover"
         @if($cardHeight) style="height: {{ $cardHeight }};" @endif
         @if($cardEager) fetchpriority="high" @else loading="lazy" @endif>

    <div class="card-body d-flex flex-column position-static">
        <h3 class="card-title h5">{{ $cardTitle }}</h3>

        @if($cardPost)
            @if($cardAuthor || $cardDate)
                <p class="d-flex flex-wrap align-items-center gap-1 py-1 text-muted small mb-2">
                    @if($cardAvatar)
                        <img src="{{ $cardAvatar }}" alt="" class="rounded-circle" width="24" height="24" loading="lazy">
                    @endif
                    @if($cardAuthor)
                        <span>{{ $cardAuthor }}</span>
                    @endif
                    @if($cardAuthor && $cardDate)
                        <span aria-hidden="true">&bull;</span>
                    @endif
                    @if($cardDate)
                        <time datetime="{{ $cardDateIso }}">{{ $cardDate }}</time>
                    @endif
                </p>
            @endif

            @if($cardExcerpt)
                <p class="card-text">{{ $cardExcerpt }}</p>
            @endif

            <a href="{{ route('posts.show', $cardPost) }}" class="btn btn-primary mt-auto align-self-start">
                {{ trans('theme::theme.news_more') }}
                <span class="visually-hidden">: {{ $cardTitle }}</span>
            </a>
        @else
            <p class="card-text text-muted mb-0">{{ trans('theme::theme.news_empty') }}</p>
        @endif
    </div>
</article>
