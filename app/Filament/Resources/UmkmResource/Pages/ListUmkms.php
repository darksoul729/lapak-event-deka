<?php

namespace App\Filament\Resources\UmkmResource\Pages;

use App\Filament\Resources\UmkmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUmkms extends ListRecords
{
    protected static string $resource = UmkmResource::class;

    protected function getHeaderActions(): array
    {
        // If tenant already has UMKM, hide create button to maintain 1 user = 1 UMKM
        if (Auth::user()?->isTenant() && Auth::user()?->umkm()->exists()) {
            return [];
        }

        return [
            Actions\CreateAction::make()->label('Buat Profil UMKM'),
        ];
    }
}
