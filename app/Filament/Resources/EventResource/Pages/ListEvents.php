<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        if (!Auth::user()?->isAdmin()) {
            return [];
        }

        return [
            Actions\CreateAction::make()->label('Tambah Event Bazar'),
        ];
    }
}
