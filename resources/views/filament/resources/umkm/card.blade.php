<div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
    <!-- Header Avatar & Category -->
    <div class="flex items-start justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div class="flex items-center space-x-3.5">
            @if($getRecord()->logo_path)
                <img src="{{ Storage::url($getRecord()->logo_path) }}" alt="{{ $getRecord()->nama_usaha }}" class="h-14 w-14 rounded-full border-2 border-indigo-500/20 object-cover shadow-sm">
            @else
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-tr from-indigo-600 to-violet-600 font-bold text-white shadow-md text-lg">
                    {{ strtoupper(substr($getRecord()->nama_usaha, 0, 2)) }}
                </div>
            @endif
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $getRecord()->nama_usaha }}
                </h3>
                <div class="mt-0.5 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                    <x-heroicon-m-user class="h-3.5 w-3.5 text-indigo-500" />
                    <span>{{ $getRecord()->nama_pemilik }}</span>
                </div>
            </div>
        </div>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold 
            @if($getRecord()->kategori_usaha === 'Kuliner') bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-900
            @elseif($getRecord()->kategori_usaha === 'Fashion') bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900
            @elseif($getRecord()->kategori_usaha === 'Kriya') bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-900
            @else bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-900 @endif">
            {{ $getRecord()->kategori_usaha }}
        </span>
    </div>

    <!-- Description & Badges -->
    <div class="my-3.5 space-y-2.5 text-xs text-gray-600 dark:text-gray-300">
        <p class="line-clamp-2 text-gray-500 dark:text-gray-400 leading-relaxed">
            "{{ $getRecord()->deskripsi_produk }}"
        </p>

        <div class="flex flex-wrap gap-2 pt-1">
            @if($getRecord()->nomor_whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $getRecord()->nomor_whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-1 font-medium text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400">
                    <x-heroicon-m-phone class="h-3.5 w-3.5 text-emerald-600" />
                    {{ $getRecord()->nomor_whatsapp }}
                </a>
            @endif

            @if($getRecord()->instagram)
                <span class="inline-flex items-center gap-1 rounded-md bg-pink-50 px-2 py-1 font-medium text-pink-700 dark:bg-pink-950/40 dark:text-pink-400">
                    <x-heroicon-m-camera class="h-3.5 w-3.5 text-pink-600" />
                    {{ $getRecord()->instagram }}
                </span>
            @endif
        </div>

        <div class="flex items-center gap-1 text-gray-400 pt-1">
            <x-heroicon-m-map-pin class="h-3.5 w-3.5 text-gray-400 shrink-0" />
            <span class="truncate">{{ $getRecord()->alamat }}</span>
        </div>
    </div>
</div>
