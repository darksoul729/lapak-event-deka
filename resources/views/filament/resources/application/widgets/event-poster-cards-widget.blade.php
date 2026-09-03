<x-filament-widgets::widget>
    <div class="space-y-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
            <div>
                <h2 class="text-xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-m-sparkles class="w-6 h-6 text-amber-500 animate-bounce" />
                    Event Bazar Terpopuler & Terbaru
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-medium">
                    Pilih event bazar favorit Anda dan daftarkan profil UMKM Anda sekarang sebelum kuota penuh!
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 border border-amber-500/30 rounded-full text-xs font-extrabold text-amber-600 dark:text-amber-400 shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                Pendaftaran Dibuka
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                        0 => ['label' => 'Kuliner & Food Fest', 'badge_bg' => 'bg-amber-500 text-gray-950', 'icon' => 'heroicon-m-fire'],
                        1 => ['label' => 'Fashion & Kriya Expo', 'badge_bg' => 'bg-purple-600 text-white', 'icon' => 'heroicon-m-sparkles'],
                        2 => ['label' => 'Youth Art & Creative', 'badge_bg' => 'bg-teal-600 text-white', 'icon' => 'heroicon-m-bolt'],
                    ];
                    $badge = $badges[$index % 3];
                @endphp

                <div class="group bg-white dark:bg-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-800 shadow-md hover:shadow-2xl hover:border-amber-500/50 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <!-- Clean Poster Image Cover -->
                    <div class="relative h-44 w-full overflow-hidden bg-gray-950">
                        <img 
                            src="{{ $posterUrl }}" 
                            alt="{{ $event->nama_event }}" 
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                        >
                        <!-- Category Badge Pill overlay on top left -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider shadow-lg {{ $badge['badge_bg'] }}">
                                <x-dynamic-component :component="$badge['icon']" class="w-3.5 h-3.5" />
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <!-- Kuota Badge overlay on top right -->
                        <div class="absolute top-3 right-3 z-10">
                            <span class="px-2.5 py-1 rounded-full bg-gray-950/80 backdrop-blur-md border border-white/20 text-[11px] font-bold text-white shadow-md">
                                Kuota: {{ $event->kuota_tenant }} Booth
                            </span>
                        </div>
                    </div>

                    <!-- Clean Content Body -->
                    <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                        <!-- Title and Location -->
                        <div class="space-y-1.5">
                            <h3 class="text-base font-extrabold text-gray-900 dark:text-white leading-snug line-clamp-2">
                                {{ $event->nama_event }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold flex items-center gap-1.5">
                                <x-heroicon-m-map-pin class="w-4 h-4 text-rose-500 shrink-0" />
                                {{ $event->lokasi }}
                            </p>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed font-normal bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                            {{ $event->deskripsi }}
                        </p>

                        <!-- Key Event Details Grid -->
                        <div class="space-y-2 pt-1 text-xs font-medium">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800/40">
                                <span class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                    <x-heroicon-m-calendar class="w-4 h-4 text-indigo-500" />
                                    Pelaksanaan
                                </span>
                                <span class="font-bold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/50 dark:border-emerald-800/50">
                                <span class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-semibold">
                                    <x-heroicon-m-tag class="w-4 h-4 text-emerald-600" />
                                    Biaya Booth
                                </span>
                                <span class="font-black text-emerald-700 dark:text-emerald-300 text-sm">
                                    Rp {{ number_format($event->biaya_booth, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-1">
                            <x-filament::button
                                tag="a"
                                :href="App\Filament\Resources\ApplicationResource::getUrl('create') . '?event_id=' . $event->id"
                                color="primary"
                                icon="heroicon-m-paper-airplane"
                                class="w-full justify-center shadow-md font-bold"
                                size="md"
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
