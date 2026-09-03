<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Event;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        if (Auth::user()?->isTenant()) {
            $umkmIds = Auth::user()?->umkms()->pluck('id') ?? [];

            $totalDaftar = Application::whereIn('umkm_id', $umkmIds)->count();
            $diterima = Application::whereIn('umkm_id', $umkmIds)->where('status_kurasi', 'diterima')->count();
            $tagihan = Payment::whereHas('application', fn ($q) => $q->whereIn('umkm_id', $umkmIds))->where('status', 'belum_bayar')->sum('jumlah_tagihan');

            return [
                Stat::make('Total Event Diikuti', $totalDaftar)
                    ->description('Jumlah pendaftaran bazar')
                    ->descriptionIcon('heroicon-m-calendar')
                    ->color('info'),
                Stat::make('Pendaftaran Diterima', $diterima)
                    ->description('Lolos tahap kurasi')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
                Stat::make('Tagihan Belum Dibayar', 'Rp ' . number_format($tagihan, 0, ',', '.'))
                    ->description('Segera lunasi biaya booth')
                    ->descriptionIcon('heroicon-m-credit-card')
                    ->color($tagihan > 0 ? 'danger' : 'gray'),
            ];
        }

        // Admin Stats
        $totalEvent = Event::count();
        $totalPendaftar = Application::count();
        $menungguKurasi = Application::where('status_kurasi', 'menunggu')->count();
        $tenantDiterima = Application::where('status_kurasi', 'diterima')->count();
        $verifikasiBayar = Payment::where('status', 'menunggu_verifikasi')->count();
        $totalPemasukan = Payment::where('status', 'lunas')->sum('jumlah_tagihan');

        return [
            Stat::make('Total Event Bazar', $totalEvent)
                ->description('Event aktif & draft')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Total Pendaftar UMKM', $totalPendaftar)
                ->description("{$menungguKurasi} pendaftar perlu kurasi")
                ->descriptionIcon('heroicon-m-users')
                ->color($menungguKurasi > 0 ? 'warning' : 'success'),

            Stat::make('Tenant Diterima', $tenantDiterima)
                ->description('Lolos kurasi bazar')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Verifikasi Pembayaran', $verifikasiBayar)
                ->description('Bukti transfer perlu dicek')
                ->descriptionIcon('heroicon-m-clock')
                ->color($verifikasiBayar > 0 ? 'rose' : 'gray'),

            Stat::make('Total Pemasukan Booth', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->description('Pembayaran terkumpul (Lunas)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('emerald'),
        ];
    }
}
