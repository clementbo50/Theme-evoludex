@php
    $aboutImage = theme_config('about.image');
    $aboutImageUrl = $aboutImage ? image_url($aboutImage) : theme_asset('img/image-placeholder.svg');
    $aboutCaption = theme_config('about.caption') ?? '';
    $aboutShowTitle = theme_config('about.show_title', true);
    $aboutShowDescription = theme_config('about.show_description', true);
@endphp
<section class="poke-split d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center min-vh-100" aria-labelledby="about-title">
    <div class="container py-5 my-auto my-lg-0">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-6">
                <p class="text-uppercase fw-bold small mb-2"> {{ trans('theme::theme.about_eyebrow') }}</p>
                <h2 id="about-title" @class(['text-break', 'mb-2', 'visually-hidden' => ! $aboutShowTitle])>{{ theme_config('about.title') ?: trans('theme::theme.about_title_invite') }}</h2>
                @if($aboutShowDescription)
                    <p class="fs-5">{{ theme_config('about.description') ?: trans('theme::theme.about_desc_invite') }}</p>
                @endif
            </div>
            <div class="col-12 col-lg-6">
                <figure class="w-75 mx-auto ms-lg-auto me-lg-0 mb-0">
                    <div class="poke-panel overflow-hidden position-relative rounded-4">
                        <img src="{{ $aboutImageUrl }}" alt="{{ $aboutCaption }}" class="img-fluid w-100 d-block" width="800" height="600" loading="lazy">
                        @if($aboutCaption)
                            <figcaption class="poke-about-tab">{{ $aboutCaption }}</figcaption>
                        @endif
                    </div>
                </figure>
            </div>
        </div>
    </div>
    <div class="poke-split-decor" aria-hidden="true"></div>
</section>
