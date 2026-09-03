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
        $firstUmkm = Auth::user()?->umkms()->first();
        if ($firstUmkm) {
            $this->activeUmkmId = (int) request()->query('umkm', $firstUmkm->id);
        }
    }

    public function getActiveUmkmProperty(): ?Umkm
    {
        if ($this->activeUmkmId) {
            return Auth::user()?->umkms()->find($this->activeUmkmId) ?? Auth::user()?->umkms()->first();
        }

        return Auth::user()?->umkms()->first();
    }

    public function getTenantUmkmsProperty()
    {
        return Auth::user()?->umkms()->get() ?? collect();
    }

    public function getView(): string
    {
        if (Auth::user()?->isTenant()) {
            return 'filament.resources.umkm.pages.tenant-profile-page';
        }

        return parent::getView();
    }

    protected function getHeaderActions(): array
    {
        if (Auth::user()?->isTenant()) {
            return [];
        }

        return [
            Actions\CreateAction::make()->label('Buat Profil UMKM Baru'),
        ];
    }
}
