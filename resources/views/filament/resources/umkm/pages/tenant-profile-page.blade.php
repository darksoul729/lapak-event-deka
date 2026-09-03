<x-filament-panels::page>
    @php
        $tenant = Auth::user();
        $umkms = $this->tenantUmkms;
        $activeUmkm = $this->activeUmkm;
    @endphp

    @if($umkms->isEmpty())
        <!-- Empty State -->
        <x-filament::section>
            <div class="flex flex-col items-center justify-center p-8 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 mb-4">
                    <x-heroicon-o-building-storefront class="h-8 w-8" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Belum Ada Profil UMKM</h3>
                <p class="mt-1 max-w-md text-sm text-gray-500 dark:text-gray-400">
                    Lengkapi profil usaha UMKM Anda untuk mulai mendaftar ke bazar dan event menarik di Samarinda.
                </p>
                <div class="mt-6">
                    <x-filament::button
                        tag="a"
                        :href="App\Filament\Resources\UmkmResource::getUrl('create')"
                        icon="heroicon-m-plus"
                        color="primary"
                    >
                        Buat Profil UMKM Pertama
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    @else
        <div class="space-y-6">
            <!-- Multi-UMKM Tabs Header with Add Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-2 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xs">
                <x-filament::tabs label="Profil Usaha Anda">
                    @foreach($umkms as $u)
                        <x-filament::tabs.item
                            :active="$activeUmkm && $activeUmkm->id === $u->id"
                            wire:click="$set('activeUmkmId', {{ $u->id }})"
                            icon="heroicon-m-building-storefront"
                        >
                            {{ $u->nama_usaha }}
                        </x-filament::tabs.item>
                    @endforeach
                </x-filament::tabs>

                <x-filament::button
                    tag="a"
                    :href="App\Filament\Resources\UmkmResource::getUrl('create')"
                    icon="heroicon-m-plus"
                    color="primary"
                    size="sm"
                    class="shrink-0"
                >
                    Tambah Usaha Baru
                </x-filament::button>
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

                <!-- Main Profile Section with Prominent Logo Header -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-4 py-1">
                            <!-- Logo Avatar Box -->
                            <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 overflow-hidden shadow-sm">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $activeUmkm->nama_usaha }}" class="h-full w-full object-contain p-1">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-600 text-lg font-black text-white">
                                        {{ strtoupper(substr($activeUmkm->nama_usaha, 0, 2)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Name, Category Badge & Owner -->
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $activeUmkm->nama_usaha }}</span>
                                    <x-filament::badge color="info" size="sm">
                                        {{ strtoupper($activeUmkm->kategori_usaha) }}
                                    </x-filament::badge>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Penanggung Jawab / Pemilik: <strong class="text-gray-800 dark:text-gray-200">{{ $activeUmkm->nama_pemilik }}</strong>
                                </p>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="headerEnd">
                        <div class="flex items-center gap-2">
                            <x-filament::button
                                tag="a"
                                :href="App\Filament\Resources\UmkmResource::getUrl('edit', ['record' => $activeUmkm->id])"
                                icon="heroicon-m-pencil-square"
                                color="gray"
                                outlined
                                size="sm"
                            >
                                Edit Profil Usaha
                            </x-filament::button>
                        </div>
                    </x-slot>

                    <!-- Profile Info Grid -->
                    <div class="space-y-6 pt-2">
                        <!-- Contact Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- WhatsApp -->
                            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 p-4 flex items-center gap-3">
                                <div class="p-2.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 shrink-0">
                                    <x-heroicon-m-phone class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-gray-500 uppercase">WhatsApp</p>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $activeUmkm->nomor_whatsapp) }}" target="_blank" class="text-sm font-bold text-emerald-600 hover:underline truncate block">
                                        {{ $activeUmkm->nomor_whatsapp }}
                                    </a>
                                </div>
                            </div>

                            <!-- Instagram -->
                            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 p-4 flex items-center gap-3">
                                <div class="p-2.5 rounded-lg bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300 shrink-0">
                                    <x-heroicon-m-camera class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-gray-500 uppercase">Instagram</p>
                                    <span class="text-sm font-bold text-pink-600 truncate block">
                                        {{ $activeUmkm->instagram ?: '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 p-4 flex items-center gap-3">
                                <div class="p-2.5 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 shrink-0">
                                    <x-heroicon-m-map-pin class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-gray-500 uppercase">Alamat Usaha</p>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                        {{ $activeUmkm->alamat }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi Produk -->
                        <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-900">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                Deskripsi Produk & Konsep Usaha
                            </h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 italic leading-relaxed">
                                "{{ $activeUmkm->deskripsi_produk }}"
                            </p>
                        </div>
                    </div>
                </x-filament::section>

                <!-- Event History Section -->
                <x-filament::section icon="heroicon-o-ticket">
                    <x-slot name="heading">
                        Riwayat Keikutsertaan Event Bazar
                    </x-slot>

                    <x-slot name="description">
                        Daftar pendaftaran event dan alokasi booth untuk <strong>{{ $activeUmkm->nama_usaha }}</strong>
                    </x-slot>

                    <x-slot name="headerEnd">
                        <x-filament::button
                            tag="a"
                            :href="App\Filament\Resources\ApplicationResource::getUrl('create')"
                            icon="heroicon-m-paper-airplane"
                            color="primary"
                            size="sm"
                        >
                            Daftar Event Bazar Baru
                        </x-filament::button>
                    </x-slot>

                    @if($applications->isEmpty())
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-calendar class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                            <p class="text-sm font-medium">Belum ada riwayat pendaftaran event untuk usaha ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($applications as $app)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-900 flex flex-col justify-between gap-4 shadow-xs">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <x-filament::badge
                                                :color="$app->status_kurasi === 'diterima' ? 'success' : ($app->status_kurasi === 'ditolak' ? 'danger' : 'warning')"
                                                :icon="$app->status_kurasi === 'diterima' ? 'heroicon-m-check-circle' : ($app->status_kurasi === 'ditolak' ? 'heroicon-m-x-circle' : 'heroicon-m-clock')"
                                                size="sm"
                                            >
                                                Kurasi {{ ucfirst($app->status_kurasi) }}
                                            </x-filament::badge>
                                            <span class="text-xs font-medium text-gray-400">
                                                {{ $app->created_at->format('d M Y') }}
                                            </span>
                                        </div>

                                        <h4 class="font-bold text-base text-gray-900 dark:text-white">
                                            {{ $app->event->nama_event }}
                                        </h4>

                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-1">
                                                <x-heroicon-m-map-pin class="h-4 w-4 text-rose-500 shrink-0" />
                                                {{ $app->event->lokasi }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <x-heroicon-m-calendar-days class="h-4 w-4 text-indigo-500 shrink-0" />
                                                {{ date('d M Y', strtotime($app->event->tanggal_pelaksanaan)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                                        <div class="flex items-center gap-1.5 font-bold text-gray-800 dark:text-gray-200">
                                            <x-heroicon-m-ticket class="h-4 w-4 text-amber-500 shrink-0" />
                                            @if($app->booth)
                                                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                                    Booth {{ $app->booth->kode_booth }} ({{ $app->booth->zona }})
                                                </span>
                                            @else
                                                <span class="text-gray-500 font-normal">
                                                    {{ $app->status_kurasi === 'diterima' ? 'Menunggu Penentuan Booth' : 'Belum Ada Booth' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if($app->payment)
                                                <x-filament::badge
                                                    :color="$app->payment->status === 'lunas' ? 'success' : ($app->payment->status === 'menunggu_verifikasi' ? 'info' : 'danger')"
                                                    size="sm"
                                                >
                                                    {{ str_replace('_', ' ', strtoupper($app->payment->status)) }}
                                                </x-filament::badge>

                                                @if(in_array($app->payment->status, ['belum_bayar', 'ditolak']))
                                                    <x-filament::button
                                                        tag="a"
                                                        :href="App\Filament\Resources\PaymentResource::getUrl('index')"
                                                        size="xs"
                                                        color="warning"
                                                        icon="heroicon-m-arrow-up-tray"
                                                    >
                                                        Bayar Sekarang
                                                    </x-filament::button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>
            @endif
        </div>
    @endif
</x-filament-panels::page>
