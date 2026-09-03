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
                    $defaultPosters = [
                        0 => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
                        1 => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=800&q=80',
                        2 => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80',
                    ];

                    $posterUrl = $event->poster_path 
                        ? \Illuminate\Support\Facades\Storage::url($event->poster_path) 
                        : ($defaultPosters[$index % 3]);

                    $badges = [
                        0 => ['label' => 'Kuliner & Food Fest', 'bg' => 'bg-amber-500 text-black', 'icon' => 'heroicon-m-fire'],
                        1 => ['label' => 'Fashion & Kriya Expo', 'bg' => 'bg-purple-500 text-white', 'icon' => 'heroicon-m-sparkles'],
                        2 => ['label' => 'Youth Art & Creative', 'bg' => 'bg-teal-500 text-black', 'icon' => 'heroicon-m-bolt'],
                    ];
                    $badge = $badges[$index % 3];
                @endphp

                <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <!-- Poster Image Header Banner -->
                    <div class="relative h-48 w-full overflow-hidden bg-gray-950 flex flex-col justify-between p-4">
                        <!-- Poster Image Background -->
                        <img 
                            src="{{ $posterUrl }}" 
                            alt="{{ $event->nama_event }}" 
                            class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-500 brightness-90"
                        >

                        <!-- Gradient Dark Overlay for High Contrast Text -->
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/50 to-black/30"></div>

                        <!-- Top Badges -->
                        <div class="relative z-10 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider shadow-md {{ $badge['bg'] }}">
                                <x-dynamic-component :component="$badge['icon']" class="w-3.5 h-3.5" />
                                {{ $badge['label'] }}
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full bg-black/60 backdrop-blur-md border border-white/20 text-[10px] font-bold text-white shadow-sm">
                                Kuota: {{ $event->kuota_tenant }} Booth
                            </span>
                        </div>

                        <!-- Poster Title & Location overlay -->
                        <div class="relative z-10 mt-auto pt-4">
                            <h3 class="text-base font-black text-white leading-snug drop-shadow-md line-clamp-2">
                                {{ $event->nama_event }}
                            </h3>
                            <p class="text-xs text-amber-300 font-semibold flex items-center gap-1 mt-1 drop-shadow">
                                <x-heroicon-m-map-pin class="w-3.5 h-3.5 shrink-0 text-amber-400" />
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
