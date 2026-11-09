@extends('layouts.app')
@section('title', __('ui.hero_title'))
@section('content')
<section class="hero campus-hero">
    <div class="campus-hero-frame">
        <img class="campus-hero-photo" src="{{ asset('images/kehidupan-mahasiswa.jpg') }}" width="1532" height="692" fetchpriority="high" alt="{{ __('ui.campus_photo') }}">
        <div class="hero-copy">
            <span class="eyebrow">{{ __('ui.hero_badge') }}</span>
            <h1>{{ __('ui.hero_title') }}</h1>
            <p>{{ __('ui.hero_text') }}</p>
            <form class="hero-search" action="{{ route('search') }}">
                <x-ui-icon name="search" size="20" />
                <input type="search" name="q" minlength="2" required aria-label="{{ __('ui.search') }}" placeholder="{{ __('ui.search_long') }}">
                <button>{{ __('ui.search') }}</button>
            </form>
        </div>
    </div>
</section>

<nav class="campus-shortcuts" aria-label="{{ __('ui.student_shortcuts') }}">
    <div class="container shortcut-grid">
        <span class="shortcut-heading">{{ __('ui.student_shortcuts') }}</span>
        @foreach(['KTM' => 'shortcut_ktm', 'UKT' => 'shortcut_ukt', 'Ijazah' => 'shortcut_diploma'] as $keyword => $label)
            <a href="{{ route('search', ['q' => $keyword]) }}">{{ __('ui.'.$label) }} <span aria-hidden="true">↗</span></a>
        @endforeach
    </div>
</nav>

<section class="section audience">
    <div class="container campus-directory" data-campus-directory>
        <div class="directory-intro">
            <h2>{{ __('ui.audience_title') }}</h2>
            <p>{{ __('ui.directory_intro') }}</p>
            <a href="{{ route('services.index') }}">{{ __('ui.all_services') }} <span aria-hidden="true">→</span></a>
        </div>
        <div class="directory-results">
            @if($categories->isNotEmpty())
                <div class="directory-filter" data-directory-filter hidden>
                    <label for="campus-category">{{ __('ui.filter_category') }}</label>
                    <div>
                        <x-ui-icon name="search" size="19" />
                        <input id="campus-category" type="search" placeholder="{{ __('ui.filter_category_example') }}">
                    </div>
                    <p class="directory-status" role="status" data-directory-status data-template="{{ __('ui.category_match_count') }}"></p>
                </div>
            @endif
            <div class="service-category-grid">
                @forelse($categories as $category)
                    <a class="service-category-card audience-row" data-directory-category href="{{ route('services.category', $category) }}">
                        <div>
                            <h3>{{ $category->name }}</h3>
                            @if($category->description)
                                <p>{{ $category->description }}</p>
                            @endif
                        </div>
                        <small>{{ $category->services_count }} {{ __('ui.available') }}</small>
                        <b aria-hidden="true">→</b>
                    </a>
                @empty
                    <p>{{ __('ui.category_directory_empty') }}</p>
                @endforelse
            </div>
            <p class="directory-empty" data-directory-empty hidden>{{ __('ui.category_no_matches') }}</p>
        </div>
    </div>
</section>

<section class="section article-showcase"><div class="container"><div class="section-head"><div><span class="eyebrow">{{ __('ui.latest_articles') }}</span><h2>{{ __('ui.article_title') }}</h2></div><div class="carousel-actions"><button type="button" class="carousel-button article-prev" aria-label="{{ __('ui.previous_article') }}">←</button><button type="button" class="carousel-button article-next" aria-label="{{ __('ui.next_article') }}">→</button><a href="{{ route('articles.index') }}">{{ __('ui.all_articles') }} →</a></div></div><div class="article-carousel" data-article-carousel><div class="article-carousel-track">@forelse($articles as $article)<article class="article-card article-slide"><img src="{{ $article->featured_image?asset('storage/'.$article->featured_image):asset('images/kehidupan-mahasiswa.jpg') }}" alt=""><div><span class="eyebrow">{{ $article->category }}</span><h3><a href="{{ route('articles.show',$article) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><small>{{ optional($article->published_at)->translatedFormat('d M Y') }} · {{ $article->author }}</small></div></article>@empty<p>{{ __('ui.article_empty') }}</p>@endforelse</div></div><div class="carousel-status" aria-live="polite"><span data-carousel-current>1</span> / <span data-carousel-total>{{ max($articles->count(), 1) }}</span></div></div></section>

<section class="section faq-home"><div class="container split"><div><span class="eyebrow">{{ __('ui.quick_answer') }}</span><h2>{{ __('ui.faq_title') }}</h2><p>{{ __('ui.faq_text') }}</p><a class="btn outline" href="{{ route('faqs') }}">{{ __('ui.all_faq') }}</a></div><div class="accordion">@forelse($faqs as $faq)<details><summary>{{ $faq->question }}<span>+</span></summary><div>{!! nl2br(e($faq->answer)) !!}@if($faq->external_url)<div class="faq-link"><a class="btn external-link" data-track="{{ $faq->question }}" href="{{ $faq->external_url }}" target="_blank" rel="noopener">{{ $faq->external_label ?: __('ui.open_related_link') }} ↗</a></div>@endif</div></details>@empty<p>FAQ sedang disiapkan.</p>@endforelse</div></div></section>

<section class="section story"><div class="container split"><div class="image-stack"><img src="{{ asset('images/fasilitas-disabilitas-01.jpg') }}" alt="Pelayanan ULT yang dapat diakses pengguna kursi roda"><img src="{{ asset('images/fasilitas-disabilitas-04.jpg') }}" alt="Jalur landai akses kursi roda"></div><div><span class="eyebrow">{{ __('ui.inclusive') }}</span><h2>{{ __('ui.inclusive_title') }}</h2><p>{{ __('ui.inclusive_text') }}</p><ul class="check-list"><li>{{ __('ui.access_location') }}</li><li>{{ __('ui.online_alternative') }}</li><li>{{ __('ui.screen_reader') }}</li></ul><a class="btn" href="{{ route('services.category','sarana-prasarana') }}">{{ __('ui.disability_services') }}</a></div></div></section>

<section class="section quick-links"><div class="container"><span class="eyebrow">{{ __('ui.official_systems') }}</span><h2>{{ __('ui.portal_title') }}</h2><div class="link-grid">@foreach($links as $link)<a class="external-link" data-track="{{ $link->name }}" href="{{ $link->url }}" target="_blank" rel="noopener"><b>{{ $link->name }}</b><span>{{ $link->description }}</span><i>↗</i></a>@endforeach</div></div></section>
@endsection
