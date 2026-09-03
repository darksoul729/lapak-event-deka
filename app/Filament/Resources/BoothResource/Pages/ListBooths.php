<?php

namespace App\Filament\Resources\BoothResource\Pages;

use App\Filament\Resources\BoothResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListBooths extends ListRecords
{
    protected static string $resource = BoothResource::class;

    protected function getHeaderActions(): array
    {
        if (!Auth::user()?->isAdmin()) {
            return [];
        }

        return [
            Actions\CreateAction::make()->label('Tambah Booth'),
        ];
    }
}
