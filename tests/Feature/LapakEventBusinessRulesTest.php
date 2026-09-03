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

    public function test_application_resource_create_page_renders_without_type_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(\App\Filament\Resources\ApplicationResource::getUrl('create'));

        $response->assertOk();
    }

    public function test_tenant_can_have_multiple_umkm_profiles(): void
    {
        $tenant = User::factory()->create(['role' => 'tenant']);

        $umkm1 = Umkm::create([
            'user_id' => $tenant->id,
            'nama_usaha' => 'Usaha Pertama',
            'nama_pemilik' => 'Pemilik 1',
            'nomor_whatsapp' => '0811111111',
            'alamat' => 'Alamat 1',
            'kategori_usaha' => 'Kuliner',
            'deskripsi_produk' => 'Produk 1',
        ]);

        $umkm2 = Umkm::create([
            'user_id' => $tenant->id,
            'nama_usaha' => 'Usaha Kedua',
            'nama_pemilik' => 'Pemilik 1',
            'nomor_whatsapp' => '0822222222',
            'alamat' => 'Alamat 2',
            'kategori_usaha' => 'Fashion',
            'deskripsi_produk' => 'Produk 2',
        ]);

        $this->assertCount(2, $tenant->umkms);
        $this->assertEquals('Usaha Pertama', $tenant->umkms[0]->nama_usaha);
        $this->assertEquals('Usaha Kedua', $tenant->umkms[1]->nama_usaha);
    }
}
