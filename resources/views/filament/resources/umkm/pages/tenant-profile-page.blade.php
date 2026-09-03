<x-filament-panels::page>
    @php
        $tenant = Auth::user();
        $umkms = $this->tenantUmkms;
        $activeUmkm = $this->activeUmkm;
    @endphp

    @if($umkms->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-xs dark:border-gray-800 dark:bg-gray-900">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 mb-4">
                <x-heroicon-o-building-storefront class="h-8 w-8" />
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Belum Ada Profil UMKM</h3>
            <p class="mt-1 max-w-md text-xs text-gray-500 dark:text-gray-400">
                Lengkapi profil usaha UMKM Anda untuk mulai mendaftar ke bazar dan event menarik di Samarinda.
            </p>
            <div class="mt-5">
                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition">
                    <x-heroicon-m-plus class="h-4 w-4" />
                    Buat Profil UMKM Pertama
                </a>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <!-- Multi-UMKM Tabs Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-4 dark:border-gray-800">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400 mr-1">Profil Usaha Anda:</span>
                    @foreach($umkms as $u)
                        <button wire:click="$set('activeUmkmId', {{ $u->id }})" 
                            class="inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer
                            {{ $activeUmkm && $activeUmkm->id === $u->id 
                                ? 'bg-indigo-600 text-white shadow-xs' 
                                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700' }}">
                            <x-heroicon-m-building-storefront class="h-4 w-4" />
                            <span>{{ $u->nama_usaha }}</span>
                        </button>
                    @endforeach
                </div>

                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" 
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">
                    <x-heroicon-m-plus class="h-3.5 w-3.5 text-indigo-600" />
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

                <!-- Store Profile Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900 border-l-4 border-l-indigo-600">
                    <!-- Top Info Row: Logo + Store Name & Actions -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-5">
                            <!-- Logo Avatar Box -->
                            <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800 overflow-hidden shadow-xs">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $activeUmkm->nama_usaha }}" class="h-full w-full object-contain p-1">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-tr from-indigo-600 to-purple-600 text-lg font-bold text-white">
                                        {{ strtoupper(substr($activeUmkm->nama_usaha, 0, 2)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Name & Category -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $activeUmkm->nama_usaha }}
                                    </h1>
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 border border-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-900">
                                        {{ $activeUmkm->kategori_usaha }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Pemilik: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $activeUmkm->nama_pemilik }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5 shrink-0">
                            <a href="{{ App\Filament\Resources\UmkmResource::getUrl('edit', ['record' => $activeUmkm->id]) }}" 
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">
                                <x-heroicon-m-pencil-square class="h-4 w-4 text-gray-500" />
                                Edit Profil
                            </a>

                            <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" 
                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition">
                                <x-heroicon-m-paper-airplane class="h-4 w-4" />
                                Ikuti Bazar Event
                            </a>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6">
                        <!-- WhatsApp -->
                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 p-3.5 dark:border-gray-800 dark:bg-gray-800/40">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-900">
                                <x-heroicon-m-phone class="h-4 w-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">WhatsApp Business</p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeUmkm->nomor_whatsapp) }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:underline truncate block">
                                    {{ $activeUmkm->nomor_whatsapp }}
                                </a>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 p-3.5 dark:border-gray-800 dark:bg-gray-800/40">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pink-50 text-pink-600 border border-pink-100 dark:bg-pink-950/60 dark:text-pink-400 dark:border-pink-900">
                                <x-heroicon-m-camera class="h-4 w-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Instagram Store</p>
                                <span class="text-xs font-semibold text-pink-600 truncate block">
                                    {{ $activeUmkm->instagram ?: 'Belum diisi' }}
                                </span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 p-3.5 dark:border-gray-800 dark:bg-gray-800/40">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-950/60 dark:text-blue-400 dark:border-blue-900">
                                <x-heroicon-m-map-pin class="h-4 w-4" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Alamat Usaha</p>
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $activeUmkm->alamat }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description Box -->
                    <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-800/40">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Deskripsi Produk & Konsep Usaha
                        </h4>
                        <p class="mt-1.5 text-xs text-gray-700 dark:text-gray-300 italic leading-relaxed">
                            "{{ $activeUmkm->deskripsi_produk }}"
                        </p>
                    </div>
                </div>

                <!-- Event History Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Riwayat Keikutsertaan Event Bazar</h2>
                            <p class="text-xs text-gray-500">Pendaftaran & status booth untuk {{ $activeUmkm->nama_usaha }}</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ $applications->count() }} Event
                        </span>
                    </div>

                    @if($applications->isEmpty())
                        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-900">
                            <x-heroicon-o-calendar class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Profil ini belum pernah mendaftar ke event bazar mana pun.</p>
                            <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                Daftarkan Usaha Ini ke Event Now &rarr;
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($applications as $app)
                                <div class="flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-800 dark:bg-gray-900">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-semibold
                                                @if($app->status_kurasi === 'diterima') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-900
                                                @elseif($app->status_kurasi === 'ditolak') bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-400 dark:border-rose-900
                                                @else bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-400 dark:border-amber-900 @endif">
                                                @if($app->status_kurasi === 'diterima')
                                                    <x-heroicon-m-check-circle class="h-3 w-3 text-emerald-600" />
                                                @elseif($app->status_kurasi === 'ditolak')
                                                    <x-heroicon-m-x-circle class="h-3 w-3 text-rose-600" />
                                                @else
                                                    <x-heroicon-m-clock class="h-3 w-3 text-amber-600" />
                                                @endif
                                                Status: Kurasi {{ ucfirst($app->status_kurasi) }}
                                            </span>
                                            <span class="text-[10px] font-medium text-gray-400">
                                                {{ $app->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $app->event->nama_event }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-gray-500 mt-2">
                                            <span class="inline-flex items-center gap-1">
                                                <x-heroicon-m-map-pin class="h-3.5 w-3.5 text-rose-500 shrink-0" />
                                                {{ $app->event->lokasi }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <x-heroicon-m-calendar-days class="h-3.5 w-3.5 text-indigo-500 shrink-0" />
                                                {{ date('d M Y', strtotime($app->event->tanggal_pelaksanaan)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800 text-[11px]">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-m-ticket class="h-4 w-4 text-amber-500" />
                                            <span class="text-gray-500">Booth:</span>
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $app->booth ? 'Booth ' . $app->booth->kode_booth . ' (' . $app->booth->zona . ')' : 'Belum Ditentukan' }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($app->payment)
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold tracking-wide
                                                    @if($app->payment->status === 'lunas') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                    @elseif($app->payment->status === 'menunggu_verifikasi') bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300
                                                    @else bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 @endif">
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
        </div>
    @endif
</x-filament-panels::page>
