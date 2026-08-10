<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#92278f">
    <meta name="description" content="@yield('description','Portal informasi dan pengarah layanan Unit Layanan Terpadu Universitas Padjadjaran.')">
    <title>@yield('title','ULT Unpad') — Unit Layanan Terpadu</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ult.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-ult.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-ult.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main">{{ __('ui.skip') }}</a>
<header class="site-header" id="header"><div class="container nav-wrap">
    <a href="{{ route('home') }}" class="brand" aria-label="ULT Unpad {{ __('ui.home') }}"><img src="{{ asset('images/logo-ult.png') }}" alt="Logo ULT Unpad"><span><b>ULT Unpad</b><small>Unit Layanan Terpadu</small></span></a>
    <button class="nav-toggle" aria-label="{{ __('ui.menu_open') }}" aria-expanded="false"><x-ui-icon name="menu" /></button>
    <nav class="nav-menu" aria-label="Navigasi utama">
        <a class="{{ request()->routeIs('home')?'active':'' }}" href="{{ route('home') }}">{{ __('ui.home') }}</a>
        <a class="{{ request()->routeIs('profile')?'active':'' }}" href="{{ route('profile') }}">{{ __('ui.profile') }}</a>
        <a class="{{ request()->routeIs('services.*')?'active':'' }}" href="{{ route('services.index') }}">{{ __('ui.services') }}</a>
        <a class="{{ request()->routeIs('articles.*')?'active':'' }}" href="{{ route('articles.index') }}">{{ __('ui.articles') }}</a>
        <a class="{{ request()->routeIs('faqs')?'active':'' }}" href="{{ route('faqs') }}">{{ __('ui.faq') }}</a>
        <a class="{{ request()->routeIs('contact')?'active':'' }}" href="{{ route('contact') }}">{{ __('ui.contact') }}</a>
    </nav>
    <div class="nav-actions">
        <button class="icon-btn search-toggle" aria-label="{{ __('ui.search_open') }}"><x-ui-icon name="search" size="20" /></button>
        <button class="icon-btn a11y-toggle" aria-label="{{ __('ui.accessibility') }}" aria-expanded="false"><x-ui-icon name="accessibility" size="20" /></button>
        <div class="language-switcher" aria-label="{{ __('ui.language') }}">
            <a class="{{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('language.switch','id') }}" lang="id">ID</a><span>/</span><a class="{{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch','en') }}" lang="en">EN</a>
        </div>
    </div>
</div></header>

<div class="search-drawer" aria-hidden="true"><div class="container"><form action="{{ route('search') }}"><label for="global-search">{{ __('ui.search_question') }}</label><div class="search-box"><input id="global-search" name="q" minlength="2" placeholder="{{ __('ui.search_placeholder') }}"><button>{{ __('ui.search') }}</button></div></form></div></div>

<div class="a11y-backdrop" hidden></div>
<aside class="a11y-panel" aria-hidden="true" aria-labelledby="a11y-title" tabindex="-1">
    <div class="a11y-header"><div><span class="a11y-kicker">ULT Unpad</span><h2 id="a11y-title">{{ __('ui.a11y_title') }} <small>(CTRL+U)</small></h2></div><button class="a11y-close" aria-label="{{ __('ui.close') }}"><x-ui-icon name="close" /></button></div>
    <div class="a11y-content">
        <p class="a11y-intro">{{ __('ui.a11y_intro') }}</p>
        <div class="a11y-grid">
            @foreach([
                ['font-increase','plus-text','text_bigger'], ['font-decrease','minus-text','text_smaller'],
                ['contrast','contrast','contrast'], ['dark','moon','dark_mode'],
                ['links','link','highlight_links'], ['spacing','spacing','text_spacing'],
                ['motion','pause','pause_motion'], ['images','image-off','hide_images'],
                ['dyslexia','dyslexia','dyslexia'], ['cursor','cursor','big_cursor'],
                ['line-height','line-height','line_height'], ['align','align','align_text'],
                ['saturation','saturation','saturation']
            ] as [$setting,$icon,$label])
            <button class="a11y-option" data-a11y="{{ $setting }}" aria-pressed="false"><x-ui-icon :name="$icon" size="31" /><span>{{ __('ui.'.$label) }}</span><small class="setting-level" aria-hidden="true"></small></button>
            @endforeach
        </div>
        <button class="a11y-reset" data-a11y="reset"><x-ui-icon name="reset" /><span>{{ __('ui.reset') }}</span></button>
    </div>
</aside>

<main id="main">@yield('content')</main>
<footer><div class="container footer-grid">
    <div><div class="brand footer-brand"><img src="{{ asset('images/logo-ult.png') }}" alt=""><span><b>ULT Unpad</b><small>Melayani dengan PASTI</small></span></div><p>{{ __('ui.footer_text') }}</p><div class="social-links">
        @foreach($footerContacts->whereIn('type',['whatsapp','instagram','tiktok']) as $social)
            <a class="external-link" data-track="{{ $social->label }}" href="{{ $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->label }}"><x-ui-icon :name="$social->type" size="20" /></a>
        @endforeach
    </div></div>
    <div><h3>{{ __('ui.explore_footer') }}</h3><a href="{{ route('profile') }}">{{ __('ui.profile') }}</a><a href="{{ route('services.index') }}">{{ __('ui.service_directory') }}</a><a href="{{ route('articles.index') }}">{{ __('ui.article_info') }}</a><a href="{{ route('faqs') }}">{{ __('ui.questions') }}</a></div>
    <div><h3>{{ __('ui.main_links') }}</h3><a href="https://www.unpad.ac.id" target="_blank" rel="noopener">Website Unpad ↗</a><a href="https://smup.unpad.ac.id" target="_blank" rel="noopener">SMUP ↗</a><a href="{{ route('contact') }}">{{ __('ui.contact_ult') }}</a></div>
    <div><h3>{{ __('ui.location') }}</h3><p>Gedung Rektorat Unpad<br>Jatinangor, Sumedang</p><small>{{ __('ui.verify_contact') }}</small></div>
</div><div class="container footer-bottom"><span>© {{ date('Y') }} ULT Universitas Padjadjaran</span><a href="/admin">{{ __('ui.admin_login') }}</a></div></footer>
<button class="scroll-top" aria-label="Scroll to top" title="Scroll to top"><x-ui-icon name="arrow-up" size="22" /></button>
</body></html>
