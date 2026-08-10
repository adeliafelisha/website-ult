@extends('layouts.app')
@section('title', __('ui.contact'))
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">{{ app()->getLocale() === 'en' ? 'Contact us' : 'Hubungi kami' }}</span><h1>{{ app()->getLocale() === 'en' ? 'We will guide you to the right channel' : 'Kami bantu arahkan ke kanal yang tepat' }}</h1><p>{{ app()->getLocale() === 'en' ? 'Search our services or FAQ first, then use one of the official contact channels below.' : 'Sebelum menghubungi, coba cari layanan atau FAQ. Untuk kebutuhan lanjutan gunakan kontak resmi berikut.' }}</p></div></section>
<section class="section"><div class="container contact-grid"><div class="contact-list">
@forelse($contacts as $contact)
    <a class="contact-card {{ $contact->url?'external-link':'' }}" @if($contact->url)data-track="{{ $contact->label }}" href="{{ $contact->url }}" @if(!str_starts_with($contact->url,'mailto:')) target="_blank" rel="noopener" @endif @endif>
        <span class="contact-icon"><x-ui-icon :name="in_array($contact->type,['email','phone','whatsapp','helpdesk','instagram','tiktok','location']) ? $contact->type : ($contact->type === 'address' ? 'location' : 'phone')" size="27" /></span>
        <div><small>{{ $contact->label }}</small><h2>{{ $contact->value }}</h2><p>{{ $contact->description }}</p></div>
    </a>
@empty
    <div class="notice"><b>Kontak resmi belum tersedia</b><p>Admin dapat menambahkan WhatsApp, Instagram, TikTok, email, telepon, dan alamat melalui CMS.</p></div>
@endforelse
</div><div><img class="rounded-image" src="{{ asset('images/fasilitas-disabilitas-01.jpg') }}" alt="Pelayanan inklusif di ULT Unpad"><div class="info-panel"><h2>{{ app()->getLocale() === 'en' ? 'In-person visit' : 'Kunjungan langsung' }}</h2><p>{{ app()->getLocale() === 'en' ? 'Bring all required documents and check the latest service hours before visiting.' : 'Pastikan membawa persyaratan yang tercantum pada halaman layanan dan periksa jam operasional terbaru.' }}</p></div></div></div></section>
@endsection
