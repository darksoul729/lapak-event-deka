<x-filament-widgets::widget>
    <div class="space-y-4 mb-6">
        <!-- Header Title Row -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-m-sparkles class="w-5 h-5 text-amber-500 animate-pulse" />
                    3 Event Bazar Terbaru
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pilih event favorit Anda dan daftarkan profil UMKM Anda sebelum kuota penuh.
                </p>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Pendaftaran Dibuka
            </span>
        </div>

        <!-- 3-Column Modern Ticket Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($this->getEvents() as $event)
                @php
                    $posterUrl = $this->getEventPoster($event);
                    $badge = $this->getEventBadge($event);
                @endphp

                <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    
                    <!-- Cover Image Container -->
                    <div class="relative h-40 w-full overflow-hidden bg-gray-950 shrink-0">
                        <img 
                            src="{{ $posterUrl }}" 
                            alt="{{ $event->nama_event }}" 
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 brightness-95"
                        >
                        <!-- Dark gradient tint overlay at top for badge legibility -->
                        <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/60 to-transparent"></div>

                        <!-- Top Badges -->
                        <div class="absolute top-3 left-3 right-3 z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md {{ $badge['bg'] }}">
                                <x-dynamic-component :component="$badge['icon']" class="w-3.5 h-3.5" />
                                {{ $badge['label'] }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-black/60 backdrop-blur-md border border-white/20 text-[10px] font-bold text-white shadow-md">
                                {{ $event->kuota_tenant }} Booth Sisa
                            </span>
                        </div>
                    </div>

                    <!-- Content Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-4">
                        
                        <!-- Title & Location -->
                        <div class="space-y-1">
                            <h3 class="text-base font-black text-gray-900 dark:text-white leading-snug line-clamp-1 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                {{ $event->nama_event }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center gap-1">
                                <x-heroicon-m-map-pin class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                                <span class="truncate">{{ $event->lokasi }}</span>
                            </p>
                        </div>

                        <!-- Description snippet -->
                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed font-normal">
                            {{ $event->deskripsi }}
                        </p>

                        <!-- Event Details Box (Pelaksanaan & Biaya) -->
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                <span class="flex items-center gap-1 text-gray-400">
                                    <x-heroicon-m-calendar class="w-4 h-4 text-indigo-500" />
                                    Pelaksanaan:
                                </span>
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                                <span class="flex items-center gap-1 text-gray-400">
                                    <x-heroicon-m-ticket class="w-4 h-4 text-emerald-500" />
                                    Biaya Booth:
                                </span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                    Rp {{ number_format($event->biaya_booth, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-1">
                            <a 
                                href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') . '?event_id=' . $event->id }}" 
                                class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-600 shadow-md hover:shadow-lg transition-all duration-200 transform group-hover:scale-[1.01]"
                            >
                                <x-heroicon-m-paper-airplane class="w-4 h-4" />
                                Daftar Event Ini
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
