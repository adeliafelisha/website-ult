<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Insight 30 Hari Terakhir</x-slot>
        <x-slot name="description">Halaman populer, perangkat pengunjung, dan sumber trafik.</x-slot>

        <div class="grid gap-6 lg:grid-cols-3">
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">Halaman terpopuler</h3>
                <div class="space-y-3">
                    @forelse ($topPages as $page)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="truncate text-gray-700 dark:text-gray-300" title="{{ $page->path }}">{{ $page->path }}</span>
                            <span class="rounded-full bg-primary-50 px-2 py-1 font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">{{ number_format($page->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data trafik.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">Perangkat</h3>
                <div class="space-y-3">
                    @forelse ($devices as $device)
                        <div class="flex items-center justify-between text-sm">
                            <span class="capitalize text-gray-700 dark:text-gray-300">{{ $device->device_type }}</span>
                            <span class="font-semibold text-gray-950 dark:text-white">{{ number_format($device->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data perangkat.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">Sumber trafik</h3>
                <div class="space-y-3">
                    @forelse ($sources as $source)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="truncate text-gray-700 dark:text-gray-300">{{ $source->source }}</span>
                            <span class="font-semibold text-gray-950 dark:text-white">{{ number_format($source->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data sumber.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
