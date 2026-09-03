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
                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 transition">
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
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 mr-1">Pilih Usaha Anda:</span>
                    @foreach($umkms as $u)
                        <button wire:click="$set('activeUmkmId', {{ $u->id }})" 
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition cursor-pointer shadow-xs
                            {{ $activeUmkm && $activeUmkm->id === $u->id 
                                ? 'bg-indigo-600 text-white ring-2 ring-indigo-600/30' 
                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700' }}">
                            <x-heroicon-m-building-storefront class="h-4 w-4" />
                            <span>{{ $u->nama_usaha }}</span>
                        </button>
                    @endforeach
                </div>

                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('create') }}" 
                    class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 transition">
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

                <!-- 2-Column Dashboard Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column (Main Info & Description) - 2 Cols -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Profile Main Box -->
                        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs dark:border-gray-800 dark:bg-gray-900 border-l-4 border-l-indigo-600">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-4">
                                    <!-- Logo Avatar Box -->
                                    <div class="relative flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800 overflow-hidden shadow-xs">
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $activeUmkm->nama_usaha }}" class="h-full w-full object-contain p-1.5">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-tr from-indigo-600 to-purple-600 text-xl font-black text-white">
                                                {{ strtoupper(substr($activeUmkm->nama_usaha, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Name & Category -->
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                                {{ $activeUmkm->nama_usaha }}
                                            </h1>
                                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-bold text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                                                {{ strtoupper($activeUmkm->kategori_usaha) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Penanggung Jawab: <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $activeUmkm->nama_pemilik }}</span>
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ App\Filament\Resources\UmkmResource::getUrl('edit', ['record' => $activeUmkm->id]) }}" 
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-700 shadow-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition shrink-0">
                                    <x-heroicon-m-pencil-square class="h-4 w-4 text-gray-500" />
                                    Edit Profil Usaha
                                </a>
                            </div>

                            <!-- Description -->
                            <div class="pt-5">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                                    Deskripsi Produk & Konsep Usaha
                                </h4>
                                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-800/40">
                                    <p class="text-xs text-gray-700 dark:text-gray-300 italic leading-relaxed">
                                        "{{ $activeUmkm->deskripsi_produk }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (Contact & Call to Action) - 1 Col -->
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-xs dark:border-gray-800 dark:bg-gray-900 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-100 pb-3 dark:border-gray-800">
                                Informasi Kontak Usaha
                            </h3>

                            <!-- WhatsApp -->
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-900">
                                    <x-heroicon-m-phone class="h-4 w-4" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">WhatsApp</p>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeUmkm->nomor_whatsapp) }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline truncate block">
                                        {{ $activeUmkm->nomor_whatsapp }}
                                    </a>
                                </div>
                            </div>

                            <!-- Instagram -->
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-pink-50 text-pink-600 border border-pink-200 dark:bg-pink-950/60 dark:text-pink-400 dark:border-pink-900">
                                    <x-heroicon-m-camera class="h-4 w-4" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Instagram</p>
                                    <span class="text-xs font-bold text-pink-600 truncate block">
                                        {{ $activeUmkm->instagram ?: 'Belum diisi' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="flex items-start gap-3 pt-1">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-400 dark:border-blue-900">
                                    <x-heroicon-m-map-pin class="h-4 w-4" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Alamat Usaha</p>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-snug">
                                        {{ $activeUmkm->alamat }}
                                    </p>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                                <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" 
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 transition">
                                    <x-heroicon-m-paper-airplane class="h-4 w-4" />
                                    Daftar Event Bazar Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event History Section (Full Width) -->
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-800">
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Riwayat Keikutsertaan Event Bazar</h2>
                            <p class="text-xs text-gray-500">Daftar pendaftaran dan status booth untuk {{ $activeUmkm->nama_usaha }}</p>
                        </div>
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ $applications->count() }} Event Registered
                        </span>
                    </div>

                    @if($applications->isEmpty())
                        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-900">
                            <x-heroicon-o-calendar class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Profil ini belum pernah mendaftar ke event bazar mana pun.</p>
                            <a href="{{ App\Filament\Resources\ApplicationResource::getUrl('create') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                Daftarkan Usaha Ini ke Event Sekarang &rarr;
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($applications as $app)
                                <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-xs dark:border-gray-800 dark:bg-gray-900 border-l-4 
                                    @if($app->status_kurasi === 'diterima') border-l-emerald-500
                                    @elseif($app->status_kurasi === 'ditolak') border-l-rose-500
                                    @else border-l-amber-500 @endif">
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold
                                                @if($app->status_kurasi === 'diterima') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                @elseif($app->status_kurasi === 'ditolak') bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300
                                                @else bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 @endif">
                                                @if($app->status_kurasi === 'diterima')
                                                    <x-heroicon-m-check-circle class="h-3.5 w-3.5 text-emerald-600" />
                                                @elseif($app->status_kurasi === 'ditolak')
                                                    <x-heroicon-m-x-circle class="h-3.5 w-3.5 text-rose-600" />
                                                @else
                                                    <x-heroicon-m-clock class="h-3.5 w-3.5 text-amber-600" />
                                                @endif
                                                Kurasi {{ ucfirst($app->status_kurasi) }}
                                            </span>
                                            <span class="text-xs font-semibold text-gray-400">
                                                {{ $app->created_at->format('d M Y') }}
                                            </span>
                                        </div>

                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                            {{ $app->event->nama_event }}
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-gray-600 dark:text-gray-300 mt-2">
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-heroicon-m-map-pin class="h-4 w-4 text-rose-500 shrink-0" />
                                                {{ $app->event->lokasi }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-heroicon-m-calendar-days class="h-4 w-4 text-indigo-500 shrink-0" />
                                                {{ date('d M Y', strtotime($app->event->tanggal_pelaksanaan)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800 text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-m-ticket class="h-4 w-4 text-amber-500" />
                                            <span class="text-gray-500">Booth:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">
                                                {{ $app->booth ? 'Booth ' . $app->booth->kode_booth . ' (' . $app->booth->zona . ')' : 'Belum Ditentukan' }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($app->payment)
                                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-extrabold tracking-wide
                                                    @if($app->payment->status === 'lunas') bg-emerald-600 text-white shadow-xs
                                                    @elseif($app->payment->status === 'menunggu_verifikasi') bg-blue-600 text-white shadow-xs
                                                    @else bg-rose-600 text-white shadow-xs @endif">
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
