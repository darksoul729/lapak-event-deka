<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Daftar Event Bazar Baru')
                ->before(function (Actions\CreateAction $action) {
                    $user = Auth::user();
                    if ($user?->isTenant() && !$user->umkm()->exists()) {
                        Notification::make()
                            ->title('Lengkapi Profil UMKM Terlebih Dahulu')
                            ->body('Anda harus mengisi Profil UMKM sebelum mendaftar event.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
