<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin Account
        $admin = User::create([
            'name' => 'Penyelenggara LapakEvent',
            'email' => 'admin@lapakevent.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Demo Tenant Account
        $demoTenantUser = User::create([
            'name' => 'Budi Kuliner Samarinda',
            'email' => 'tenant@lapakevent.id',
            'password' => Hash::make('password'),
            'role' => 'tenant',
        ]);

        $demoUmkm = Umkm::create([
            'user_id' => $demoTenantUser->id,
            'nama_usaha' => 'Es Teh Solo Samarinda',
            'nama_pemilik' => 'Budi Santoso',
            'nomor_whatsapp' => '081234567890',
            'alamat' => 'Jl. M. Yamin No. 45, Samarinda',
            'kategori_usaha' => 'Kuliner',
            'deskripsi_produk' => 'Es Teh Manis Jumbo dengan aneka varian rasa (Melati, Lemon, Milk Tea).',
            'instagram' => '@estehsolo_smr',
        ]);

        // 3. Create Main Demo Event
        $event = Event::create([
            'nama_event' => 'Samarinda Culinary Market 2026',
            'deskripsi' => 'Bazar Kuliner UMKM terbesar di Kota Samarinda yang menghadirkan 30 tenant pilihan dengan lebih dari 10.000 pengunjung.',
            'lokasi' => 'GOR Segiri Samarinda',
            'tanggal_pelaksanaan' => '2026-10-15',
            'batas_pendaftaran' => '2026-09-30 23:59:59',
            'kuota_tenant' => 30,
            'biaya_booth' => 1500000.00,
            'status' => 'pendaftaran_dibuka',
        ]);

        // Secondary Demo Event
        Event::create([
            'nama_event' => 'Kaltim Fashion & Handicraft Expo 2026',
            'deskripsi' => 'Pameran fashion batik lokal dan produk kerajinan tangan UMKM se-Kalimantan Timur.',
            'lokasi' => 'Convention Hall Sempaja Samarinda',
            'tanggal_pelaksanaan' => '2026-11-20',
            'batas_pendaftaran' => '2026-11-01 23:59:59',
            'kuota_tenant' => 20,
            'biaya_booth' => 2000000.00,
            'status' => 'pendaftaran_dibuka',
        ]);

        // 4. Create 30 Booths for Main Event (A-01 to A-15 Food Court, B-01 to B-15 Reguler)
        $booths = [];
        for ($i = 1; $i <= 15; $i++) {
            $code = 'A-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $booths[] = Booth::create([
                'event_id' => $event->id,
                'kode_booth' => $code,
                'zona' => 'Food Court',
                'ukuran' => '3x3 m',
                'harga' => 1500000.00,
                'status' => 'tersedia',
            ]);
        }
        for ($i = 1; $i <= 15; $i++) {
            $code = 'B-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $booths[] = Booth::create([
                'event_id' => $event->id,
                'kode_booth' => $code,
                'zona' => 'Reguler',
                'ukuran' => '3x3 m',
                'harga' => 1500000.00,
                'status' => 'tersedia',
            ]);
        }

        // 5. Create Demo Application for Demo Tenant
        $demoApp = Application::create([
            'event_id' => $event->id,
            'umkm_id' => $demoUmkm->id,
            'konsep_booth' => 'Stand booth es segar dengan banner menarik, memerlukan 1 colokan listrik 450W.',
            'status_kurasi' => 'diterima',
            'nilai_kurasi' => 92,
            'catatan_admin' => 'Konsep sangat siap dan menarik, produk diminati pengunjung.',
            'booth_id' => $booths[0]->id,
        ]);

        $booths[0]->update(['status' => 'terisi']);

        Payment::create([
            'application_id' => $demoApp->id,
            'nomor_tagihan' => 'INV/20260903/0001',
            'jumlah_tagihan' => 1500000.00,
            'status' => 'lunas',
            'tanggal_dibayar' => now(),
        ]);

        // 6. Generate 49 Additional UMKM Applicants (Total 50 Applicants)
        $categories = ['Kuliner', 'Fashion', 'Kriya', 'Jasa', 'Elektronik'];
        $sampleProducts = [
            'Pentol Bakar Pedas', 'Nasi Goreng Samarinda', 'Ayam Geprek Sambal Matah', 
            'Batik Tenun Samarinda', 'Kerajinan Mandau Kayu', 'Kopi Susu Gula Aren',
            'Salad Buah Segar', 'Kebab Turkiye', 'Dimsum Ayam Udang', 'Kain Sarung Samarinda'
        ];

        for ($k = 2; $k <= 50; $k++) {
            $u = User::create([
                'name' => "Tenant UMKM #{$k}",
                'email' => "tenant{$k}@lapakevent.id",
                'password' => Hash::make('password'),
                'role' => 'tenant',
            ]);

            $cat = $categories[array_rand($categories)];
            $prod = $sampleProducts[array_rand($sampleProducts)];

            $umkm = Umkm::create([
                'user_id' => $u->id,
                'nama_usaha' => "UMKM {$prod} #{$k}",
                'nama_pemilik' => "Pemilik UMKM #{$k}",
                'nomor_whatsapp' => '0812' . rand(10000000, 99999999),
                'alamat' => "Jl. Ahmad Yani No. {$k}, Samarinda",
                'kategori_usaha' => $cat,
                'deskripsi_produk' => "Menjual {$prod} khas Samarinda dengan kualitas terbaik.",
                'instagram' => "@umkm{$k}_smr",
            ]);

            // 29 Accepted Tenants (bringing total accepted to 30)
            if ($k <= 30) {
                $boothIndex = $k - 1; // index 1 to 29
                $booth = $booths[$boothIndex];

                $app = Application::create([
                    'event_id' => $event->id,
                    'umkm_id' => $umkm->id,
                    'konsep_booth' => "Menyiapkan stand {$prod} dengan fasilitas tempat duduk kecil & display banner.",
                    'status_kurasi' => 'diterima',
                    'nilai_kurasi' => rand(75, 98),
                    'catatan_admin' => 'Lolos kurasi, siap berpartisipasi.',
                    'booth_id' => $booth->id,
                ]);

                $booth->update(['status' => 'terisi']);

                Payment::create([
                    'application_id' => $app->id,
                    'nomor_tagihan' => 'INV/20260903/' . str_pad($k, 4, '0', STR_PAD_LEFT),
                    'jumlah_tagihan' => 1500000.00,
                    'status' => 'lunas',
                    'tanggal_dibayar' => now()->subMinutes(rand(10, 500)),
                ]);
            }
            // 10 Waiting Curation
            elseif ($k <= 40) {
                Application::create([
                    'event_id' => $event->id,
                    'umkm_id' => $umkm->id,
                    'konsep_booth' => "Konsep pameran produk {$prod}.",
                    'status_kurasi' => 'menunggu',
                ]);
            }
            // 5 Under Review
            elseif ($k <= 45) {
                Application::create([
                    'event_id' => $event->id,
                    'umkm_id' => $umkm->id,
                    'konsep_booth' => "Konsep pameran produk {$prod}.",
                    'status_kurasi' => 'sedang_ditinjau',
                ]);
            }
            // 5 Rejected
            else {
                Application::create([
                    'event_id' => $event->id,
                    'umkm_id' => $umkm->id,
                    'konsep_booth' => "Konsep produk {$prod}.",
                    'status_kurasi' => 'ditolak',
                    'nilai_kurasi' => 50,
                    'catatan_admin' => 'Kuota kategori produk sejenis sudah penuh.',
                ]);
            }
        }
    }
}
