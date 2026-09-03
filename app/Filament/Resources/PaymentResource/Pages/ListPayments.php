<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        if (!Auth::user()?->isAdmin()) {
            return [];
        }

        return [
            Actions\CreateAction::make()->label('Catat Transaksi Manual'),
        ];
    }
}
