<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('umkm_id')->constrained('umkms')->cascadeOnDelete();
            $table->text('konsep_booth');
            $table->string('status_kurasi')->default('menunggu'); // menunggu, sedang_ditinjau, diterima, ditolak
            $table->integer('nilai_kurasi')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('booth_id')->nullable()->constrained('booths')->nullOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'umkm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
