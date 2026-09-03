<x-filament-widgets::widget>
    <div class="space-y-3 mb-4">
        <!-- Header Title Row -->
        <div class="flex items-center justify-between gap-2 border-b border-gray-200 dark:border-gray-800 pb-2.5">
            <div>
                <h2 class="text-base font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-m-sparkles class="w-5 h-5 text-amber-500 animate-pulse" />
                    3 Event Bazar Terbaru
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Pilih event dan daftarkan profil UMKM Anda sebelum kuota booth penuh.
                </p>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/10 border border-amber-500/30 rounded-full text-[11px] font-bold text-amber-600 dark:text-amber-400 shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Pendaftaran Dibuka
            </span>
        </div>

        <!-- 3-Column Compact Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($this->getEvents() as $event)
                @php
                    $posterUrl = $this->getEventPoster($event);
                    $badge = $this->getEventBadge($event);
                @endphp

                <div class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-amber-500/40 transition-all duration-200 overflow-hidden flex flex-col justify-between">
                    <!-- Compact Poster Banner Container (Fixed 128px / h-32) -->
                    <div class="relative h-32 w-full overflow-hidden bg-gray-950 shrink-0">
                        <img 
                            src="{{ $posterUrl }}" 
                            alt="{{ $event->nama_event }}" 
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                        >
                        <!-- Category Badge Pill -->
                        <div class="absolute top-2 left-2 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider shadow {{ $badge['bg'] }}">
                                <x-dynamic-component :component="$badge['icon']" class="w-3 h-3" />
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <!-- Kuota Badge -->
                        <div class="absolute top-2 right-2 z-10">
                            <span class="px-2 py-0.5 rounded bg-black/75 backdrop-blur-sm border border-white/20 text-[10px] font-bold text-white shadow">
                                Sisa {{ $event->kuota_tenant }} Booth
                            </span>
                        </div>
                    </div>

                    <!-- Compact Content Body -->
                    <div class="p-3.5 space-y-3 flex-1 flex flex-col justify-between">
                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white leading-tight line-clamp-1">
                                {{ $event->nama_event }}
                            </h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold flex items-center gap-1">
                                <x-heroicon-m-map-pin class="w-3.5 h-3.5 text-rose-500 shrink-0" />
                                {{ $event->lokasi }}
                            </p>
                        </div>

                        <p class="text-[11px] text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed font-normal bg-gray-50 dark:bg-gray-800/60 p-2 rounded border border-gray-100 dark:border-gray-800">
                            {{ $event->deskripsi }}
                        </p>

                        <!-- Key Event Details Grid -->
                        <div class="space-y-1.5 pt-1 text-[11px]">
                            <div class="flex items-center justify-between p-1.5 rounded bg-gray-50 dark:bg-gray-800/40">
                                <span class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                    <x-heroicon-m-calendar class="w-3.5 h-3.5 text-indigo-500" />
                                    Pelaksanaan:
                                </span>
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-1.5 rounded bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/40 dark:border-emerald-800/40">
                                <span class="flex items-center gap-1 text-emerald-700 dark:text-emerald-300 font-semibold">
                                    <x-heroicon-m-tag class="w-3.5 h-3.5 text-emerald-600" />
                                    Biaya Booth:
                                </span>
                                <span class="font-black text-emerald-700 dark:text-emerald-300 text-xs">
                                    Rp {{ number_format($event->biaya_booth, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Compact Action Button -->
                        <div class="pt-1">
                            <x-filament::button
                                tag="a"
                                :href="App\Filament\Resources\ApplicationResource::getUrl('create') . '?event_id=' . $event->id"
                                color="primary"
                                icon="heroicon-m-paper-airplane"
                                class="w-full justify-center font-bold text-xs"
                                size="xs"
                            >
                                Daftar Event Ini
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
