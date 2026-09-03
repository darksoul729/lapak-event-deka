<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Umkm;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if ($user?->isTenant()) {
            if (empty($data['umkm_id'])) {
                $umkm = Umkm::where('user_id', $user->id)->first();
                if (!$umkm) {
                    Notification::make()
                        ->title('Profil UMKM Belum Ada')
                        ->body('Silakan buat sekurang-kurangnya satu Profil UMKM terlebih dahulu.')
                        ->danger()
                        ->send();
                    $this->halt();
                }
                $data['umkm_id'] = $umkm->id;
            }
        }

        // Check duplicate application
        $exists = Application::where('event_id', $data['event_id'])
            ->where('umkm_id', $data['umkm_id'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Pendaftaran Gagal')
                ->body('UMKM Anda sudah terdaftar pada event ini!')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['status_kurasi'] = 'menunggu';

        return $data;
    }
}
