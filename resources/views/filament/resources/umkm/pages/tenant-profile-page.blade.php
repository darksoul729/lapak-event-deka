<x-filament-panels::page>
    @php
        $tenant = Auth::user();
        $umkms = $this->tenantUmkms;
        $activeUmkm = $this->activeUmkm;
    @endphp

    @if($umkms->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 mb-4">
                <x-heroicon-o-building-storefront class="h-10 w-10" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belum Ada Profil UMKM</h3>
            <p class="mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">
                Lengkapi profil usaha UMKM Anda untuk mulai mendaftar ke bazar dan event menarik di Samarinda.
            </p>
            <div class="mt-6">
                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition">
                    <x-heroicon-m-plus class="h-5 w-5" />
                    Buat Profil UMKM Pertama
                </a>
            </div>
        </div>
    @else
        <!-- Multi-UMKM Tabs Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 pb-4 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 mr-2">Profil Usaha Anda:</span>
                @foreach($umkms as $u)
                    <button wire:click="$set('activeUmkmId', {{ $u->id }})" 
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition shadow-sm
                        {{ $activeUmkm && $activeUmkm->id === $u->id 
                            ? 'bg-indigo-600 text-white shadow-indigo-500/20 font-bold' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700' }}">
                        <x-heroicon-m-building-storefront class="h-4 w-4" />
                        <span>{{ $u->nama_usaha }}</span>
                    </button>
                @endforeach
            </div>

            <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" 
                class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 dark:border-indigo-900/50 dark:bg-indigo-950/40 dark:text-indigo-400 transition">
                <x-heroicon-m-plus class="h-4 w-4" />
                Tambah Usaha Baru
            </a>
        </div>

        @if($activeUmkm)
            @php
                $logoUrl = null;
                if ($activeUmkm->logo_path) {
                    $logoUrl = str_starts_with($activeUmkm->logo_path, 'http')
                        ? $activeUmkm->logo_path
                        : Storage::url($activeUmkm->logo_path);
                }
                $applications = \App\Models\Application::where('umkm_id', $activeUmkm->id)->with('event', 'booth', 'payment')->get();
            @endphp

            <!-- Hero Store Card Banner -->
            <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <!-- Cover Header Banner -->
                <div class="relative h-40 w-full bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute top-4 right-4 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/90 px-3.5 py-1 text-xs font-bold text-gray-800 shadow-md backdrop-blur dark:bg-gray-900/90 dark:text-white">
                            <x-heroicon-m-sparkles class="h-3.5 w-3.5 text-amber-500" />
                            {{ $activeUmkm->kategori_usaha }}
                        </span>
                    </div>
                </div>

                <!-- Profile Info Header Content -->
                <div class="relative px-6 pb-6 pt-0">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 -mt-14 mb-6">
                        <!-- Avatar / Logo -->
                        <div class="flex items-end space-x-4">
                            <div class="relative flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl border-4 border-white bg-white shadow-xl dark:border-gray-900 dark:bg-gray-800 overflow-hidden">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $activeUmkm->nama_usaha }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-tr from-indigo-600 to-purple-600 text-2xl font-bold text-white">
                                        {{ strtoupper(substr($activeUmkm->nama_usaha, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-1">
                                <div class="flex items-center gap-2">
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-none">
                                        {{ $activeUmkm->nama_usaha }}
                                    </h1>
                                    <x-heroicon-m-check-badge class="h-6 w-6 text-indigo-500 shrink-0" title="Terverifikasi" />
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                                    Pemilik: <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $activeUmkm->nama_pemilik }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Header Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ App\Filament\Resources\UmkmResource::getUrl('edit', ['record' => $activeUmkm->id]) }}" 
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">
                                <x-heroicon-m-pencil-square class="h-4 w-4 text-gray-500" />
                                Edit Profil
                            </a>

                            <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" 
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:from-indigo-500 hover:to-purple-500 transition">
                                <x-heroicon-m-paper-airplane class="h-4 w-4" />
                                Ikuti Bazar Event
                            </a>
                        </div>
                    </div>

                    <!-- Quick Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-6 dark:border-gray-800">
                        <!-- Contact Details -->
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                                <x-heroicon-m-phone class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 uppercase font-semibold">WhatsApp Business</p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeUmkm->nomor_whatsapp) }}" target="_blank" class="text-sm font-bold text-emerald-600 hover:underline truncate block">
                                    {{ $activeUmkm->nomor_whatsapp }}
                                </a>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-600 dark:bg-pink-950/60 dark:text-pink-400">
                                <x-heroicon-m-camera class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 uppercase font-semibold">Instagram Store</p>
                                <span class="text-sm font-bold text-pink-600 truncate block">
                                    {{ $activeUmkm->instagram ?: 'Belum diisi' }}
                                </span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center gap-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                                <x-heroicon-m-map-pin class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 uppercase font-semibold">Alamat Usaha</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $activeUmkm->alamat }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="mt-6 rounded-2xl bg-indigo-50/50 p-5 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/30">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1">
                            Deskripsi Produk & Konsep Usaha
                        </h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic leading-relaxed">
                            "{{ $activeUmkm->deskripsi_produk }}"
                        </p>
                    </div>
                </div>
            </div>

            <!-- Event Applications Section -->
            <div class="mt-8 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Keikutsertaan Event Bazar</h2>
                        <p class="text-xs text-gray-500">Daftar pendaftaran dan status booth untuk profil {{ $activeUmkm->nama_usaha }}</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ $applications->count() }} Event
                    </span>
                </div>

                @if($applications->isEmpty())
                    <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-900">
                        <x-heroicon-o-calendar class="mx-auto h-10 w-10 text-gray-400 mb-2" />
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Profil ini belum pernah mendaftar ke event bazar mana pun.</p>
                        <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:underline">
                            Daftarkan Usaha Ini ke Event Now &rarr;
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($applications as $app)
                            <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold
                                            @if($app->status_kurasi === 'diterima') bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400
                                            @elseif($app->status_kurasi === 'ditolak') bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400
                                            @else bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 @endif">
                                            Status: Kurasi {{ ucfirst($app->status_kurasi) }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $app->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $app->event->nama_event }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        📍 {{ $app->event->lokasi }} | 📅 {{ date('d M Y', strtotime($app->event->tanggal_pelaksanaan)) }}
                                    </p>
                                </div>

                                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800 text-xs">
                                    <div>
                                        <span class="text-gray-400">Booth:</span>
                                        <span class="font-bold text-gray-800 dark:text-gray-200">
                                            {{ $app->booth ? 'Booth ' . $app->booth->kode_booth . ' (' . $app->booth->zona . ')' : 'Belum Ditentukan' }}
                                        </span>
                                    </div>
                                    <div>
                                        @if($app->payment)
                                            <span class="rounded-md px-2 py-1 font-semibold 
                                                @if($app->payment->status === 'lunas') bg-emerald-50 text-emerald-600
                                                @elseif($app->payment->status === 'menunggu_verifikasi') bg-blue-50 text-blue-600
                                                @else bg-rose-50 text-rose-600 @endif">
                                                {{ str_replace('_', ' ', strtoupper($app->payment->status)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif
</x-filament-panels::page>
