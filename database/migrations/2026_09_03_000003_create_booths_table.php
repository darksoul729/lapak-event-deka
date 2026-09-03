<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('kode_booth'); // e.g. A-01, B-02
            $table->string('zona')->default('Reguler'); // VIP, Reguler, Food Court
            $table->string('ukuran')->default('3x3 m');
            $table->decimal('harga', 12, 2)->nullable();
            $table->string('status')->default('tersedia'); // tersedia, dipesan, terisi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booths');
    }
};
