<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('nomor_whatsapp');
            $table->text('alamat');
            $table->string('kategori_usaha'); // Kuliner, Fashion, Kriya, Jasa, Elektronik, Lainnya
            $table->text('deskripsi_produk');
            $table->string('instagram')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
