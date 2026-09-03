<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('nomor_tagihan')->unique();
            $table->decimal('jumlah_tagihan', 12, 2);
            $table->string('bukti_pembayaran_path')->nullable();
            $table->string('status')->default('belum_bayar'); // belum_bayar, menunggu_verifikasi, lunas, ditolak
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('tanggal_dibayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
