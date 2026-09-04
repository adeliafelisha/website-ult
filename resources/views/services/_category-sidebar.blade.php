<aside class="category-sidebar" aria-label="{{ __('ui.other_categories') }}">
    <strong>{{ __('ui.other_categories') }}</strong>
    @foreach($categories as $item)
        <div class="sidebar-category @if($item->is($activeCategory)) active @endif">
            <a class="sidebar-category-heading" href="{{ route('services.category',$item) }}">
                <span>{{ $item->name }}</span><small>{{ $item->services_count }}</small>
            </a>
            @if($item->services->isNotEmpty())
                <ul>
                    @foreach($item->services as $sidebarService)
                        <li><a class="@if(isset($activeService) && $activeService->is($sidebarService)) current @endif" href="{{ route('services.show',$sidebarService) }}" @if(isset($activeService) && $activeService->is($sidebarService)) aria-current="page" @endif>{{ $sidebarService->title }}</a></li>
                    @endforeach
                </ul>
            @else
                <p>{{ __('ui.no_services_short') }}</p>
            @endif
        </div>
    @endforeach
</aside>
