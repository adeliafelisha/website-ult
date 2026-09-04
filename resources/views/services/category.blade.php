@extends('layouts.app')
@section('title', $category->name)
@section('description', $category->description)
@section('content')
<section class="category-hero"><div class="container"><div class="breadcrumb"><a href="{{ route('home') }}">{{ __('ui.home') }}</a><span>/</span><a href="{{ route('services.index') }}">{{ __('ui.services') }}</a><span>/</span><span>{{ $category->name }}</span></div><span class="eyebrow">{{ __('ui.service_category') }}</span><h1>{{ $category->name }}</h1><p>{{ $category->description }}</p><div class="category-hero-meta"><strong>{{ $services->total() }}</strong><span>{{ __('ui.services_available_in_category') }}</span></div></div></section>
<section class="section"><div class="container category-directory">@include('services._category-sidebar',['activeCategory'=>$category])<div><div class="category-content-head"><div><span class="eyebrow">{{ __('ui.service_list') }}</span><h2>{{ __('ui.choose_specific_service') }}</h2></div></div><div class="card-grid category-service-grid">@forelse($services as $service)<x-service-card :service="$service"/>@empty<div class="empty-state"><h2>{{ __('ui.category_empty') }}</h2><p>{{ __('ui.category_empty_text') }}</p></div>@endforelse</div>{{ $services->links() }}</div></div></section>
@endsection
