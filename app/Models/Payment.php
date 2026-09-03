<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'nomor_tagihan',
        'jumlah_tagihan',
        'bukti_pembayaran_path',
        'status',
        'alasan_penolakan',
        'tanggal_dibayar',
    ];

    protected $casts = [
        'jumlah_tagihan' => 'decimal:2',
        'tanggal_dibayar' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
