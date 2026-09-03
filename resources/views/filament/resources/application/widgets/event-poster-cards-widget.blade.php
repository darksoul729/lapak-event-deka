<x-filament-widgets::widget>
    <div class="space-y-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-m-sparkles class="w-5 h-5 text-amber-500 animate-pulse" />
                    3 Event Bazar Terbaru & Terpopuler
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Pilih event bazar favorit Anda dan daftarkan profil UMKM Anda sekarang juga sebelum kuota penuh!
                </p>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 rounded-full text-xs font-bold text-amber-600 dark:text-amber-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Pendaftaran Buka
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($this->getEvents() as $index => $event)
                @php
                    $themes = [
                        0 => [
                            'bg' => 'from-amber-600 via-orange-600 to-red-700',
                            'badge' => 'Kuliner & Food Fest',
                            'badge_color' => 'bg-amber-400 text-gray-950',
                            'icon' => 'heroicon-m-fire',
                            'accent' => 'text-amber-400',
                        ],
                        1 => [
                            'bg' => 'from-indigo-600 via-purple-600 to-pink-700',
                            'badge' => 'Fashion & Kriya Expo',
                            'badge_color' => 'bg-purple-300 text-gray-950',
                            'icon' => 'heroicon-m-sparkles',
                            'accent' => 'text-purple-300',
                        ],
                        2 => [
                            'bg' => 'from-emerald-600 via-teal-600 to-cyan-700',
                            'badge' => 'Youth Art & Creative',
                            'badge_color' => 'bg-teal-300 text-gray-950',
                            'icon' => 'heroicon-m-bolt',
                            'accent' => 'text-teal-300',
                        ],
                    ];
                    $theme = $themes[$index % 3];
                @endphp

                <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <!-- Poster Header Graphics -->
                    <div class="relative h-44 bg-gradient-to-br {{ $theme['bg'] }} p-5 text-white flex flex-col justify-between overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/20 via-transparent to-black/30 pointer-events-none"></div>
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 blur-xl group-hover:scale-125 transition-transform duration-500"></div>

                        <!-- Top Badge Row -->
                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider shadow-sm {{ $theme['badge_color'] }}">
                                <x-dynamic-component :component="$theme['icon']" class="w-3.5 h-3.5" />
                                {{ $theme['badge'] }}
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full bg-black/40 backdrop-blur-md border border-white/20 text-[10px] font-bold text-white">
                                Sisa Kuota: {{ $event->kuota_tenant }} Booth
                            </span>
                        </div>

                        <!-- Event Title in Poster -->
                        <div class="relative z-10 mt-auto">
                            <h3 class="text-lg font-black leading-snug drop-shadow-md line-clamp-2 text-white">
                                {{ $event->nama_event }}
                            </h3>
                            <p class="text-xs text-white/90 font-medium flex items-center gap-1 mt-1">
                                <x-heroicon-m-map-pin class="w-3.5 h-3.5 shrink-0 opacity-80" />
                                {{ $event->lokasi }}
                            </p>
                        </div>
                    </div>

                    <!-- Poster Card Body -->
                    <div class="p-4 space-y-3 flex-1 flex flex-col justify-between bg-white dark:bg-gray-900">
                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed font-normal">
                            {{ $event->deskripsi }}
                        </p>

                        <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800 text-xs">
                            <div class="flex items-center justify-between text-gray-700 dark:text-gray-300 font-medium">
                                <span class="flex items-center gap-1 text-gray-500">
                                    <x-heroicon-m-calendar class="w-4 h-4 text-gray-400" />
                                    Pelaksanaan:
                                </span>
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-700 dark:text-gray-300 font-medium">
                                <span class="flex items-center gap-1 text-gray-500">
                                    <x-heroicon-m-tag class="w-4 h-4 text-emerald-500" />
                                    Biaya Booth:
                                </span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                    Rp {{ number_format($event->biaya_booth, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2">
                            <x-filament::button
                                tag="a"
                                :href="App\Filament\Resources\ApplicationResource::getUrl('create') . '?event_id=' . $event->id"
                                color="primary"
                                icon="heroicon-m-paper-airplane"
                                class="w-full justify-center group-hover:scale-[1.02] transition-transform duration-200"
                                size="sm"
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
