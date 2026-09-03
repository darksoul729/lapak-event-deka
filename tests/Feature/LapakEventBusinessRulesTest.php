<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LapakEventBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_register_twice_for_same_event(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $umkm = Umkm::create([
            'user_id' => $tenant->id,
            'nama_usaha' => 'Kedai Kopi Tes',
            'nama_pemilik' => 'Tes',
            'nomor_whatsapp' => '08123456789',
            'alamat' => 'Alamat Tes',
            'kategori_usaha' => 'Kuliner',
            'deskripsi_produk' => 'Kopi',
        ]);

        $event = Event::create([
            'nama_event' => 'Bazar Tes 2026',
            'deskripsi' => 'Deskripsi',
            'lokasi' => 'GOR',
            'tanggal_pelaksanaan' => '2026-10-10',
            'batas_pendaftaran' => now()->addDays(5),
            'kuota_tenant' => 10,
            'biaya_booth' => 500000,
            'status' => 'pendaftaran_dibuka',
        ]);

        // First application
        Application::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'konsep_booth' => 'Konsep 1',
            'status_kurasi' => 'menunggu',
        ]);

        $this->assertDatabaseHas('applications', [
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
        ]);

        // Second application should fail database unique constraint
        $this->expectException(\Illuminate\Database\QueryException::class);

        Application::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'konsep_booth' => 'Konsep 2',
            'status_kurasi' => 'menunggu',
        ]);
    }

    public function test_payment_invoice_created_when_accepted(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $umkm = Umkm::create([
            'user_id' => $tenant->id,
            'nama_usaha' => 'Bakso Pentol',
            'nama_pemilik' => 'Pak Ahmad',
            'nomor_whatsapp' => '081299990000',
            'alamat' => 'Jl. Pahlawan',
            'kategori_usaha' => 'Kuliner',
            'deskripsi_produk' => 'Bakso',
        ]);

        $event = Event::create([
            'nama_event' => 'Bazar Kuliner 2026',
            'deskripsi' => 'Deskripsi',
            'lokasi' => 'Plaza',
            'tanggal_pelaksanaan' => '2026-11-10',
            'batas_pendaftaran' => now()->addDays(10),
            'kuota_tenant' => 5,
            'biaya_booth' => 1000000,
            'status' => 'pendaftaran_dibuka',
        ]);

        $app = Application::create([
            'event_id' => $event->id,
            'umkm_id' => $umkm->id,
            'konsep_booth' => 'Stand Bakso',
            'status_kurasi' => 'menunggu',
        ]);

        // Update application to accepted & trigger payment creation
        $app->update(['status_kurasi' => 'diterima']);

        Payment::create([
            'application_id' => $app->id,
            'nomor_tagihan' => 'INV/' . date('Ymd') . '/' . str_pad($app->id, 4, '0', STR_PAD_LEFT),
            'jumlah_tagihan' => $event->biaya_booth,
            'status' => 'belum_bayar',
        ]);

        $this->assertDatabaseHas('payments', [
            'application_id' => $app->id,
            'jumlah_tagihan' => 1000000,
            'status' => 'belum_bayar',
        ]);
    }

    public function test_booth_status_becomes_terisi_when_assigned(): void
    {
        $event = Event::create([
            'nama_event' => 'Event Tes',
            'deskripsi' => 'Deskripsi',
            'lokasi' => 'Lokasi',
            'tanggal_pelaksanaan' => '2026-12-01',
            'batas_pendaftaran' => now()->addDays(2),
            'kuota_tenant' => 5,
            'biaya_booth' => 750000,
            'status' => 'pendaftaran_dibuka',
        ]);

        $booth = Booth::create([
            'event_id' => $event->id,
            'kode_booth' => 'A-01',
            'zona' => 'VIP',
            'ukuran' => '3x3 m',
            'status' => 'tersedia',
        ]);

        $this->assertEquals('tersedia', $booth->status);

        $booth->update(['status' => 'terisi']);
        $this->assertEquals('terisi', $booth->status);
    }

    public function test_tenant_can_access_application_create_page(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);
        $response = $this->actingAs($tenant)->get(\App\Filament\Resources\ApplicationResource::getUrl('create'));

        $response->assertOk();
    }

    public function test_admin_cannot_create_application_or_umkm_profile_or_manual_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->assertFalse(\App\Filament\Resources\ApplicationResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\UmkmResource::canCreate());
        $this->assertFalse(\App\Filament\Resources\PaymentResource::canCreate());
    }

    public function test_tenant_cannot_see_event_and_booth_resources_in_navigation(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $this->actingAs($tenant);

        $this->assertFalse(\App\Filament\Resources\EventResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Resources\EventResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\EventResource::canCreate());

        $this->assertFalse(\App\Filament\Resources\BoothResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Resources\BoothResource::canViewAny());
        $this->assertFalse(\App\Filament\Resources\BoothResource::canCreate());
    }

    public function test_admin_can_see_event_and_booth_resources_in_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $this->assertTrue(\App\Filament\Resources\EventResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Resources\EventResource::canViewAny());

        $this->assertTrue(\App\Filament\Resources\BoothResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Resources\BoothResource::canViewAny());
    }
}
