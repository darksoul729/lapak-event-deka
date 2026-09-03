<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_event',
        'poster_path',
        'deskripsi',
        'lokasi',
        'tanggal_pelaksanaan',
        'batas_pendaftaran',
        'kuota_tenant',
        'biaya_booth',
        'status',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'batas_pendaftaran' => 'datetime',
        'biaya_booth' => 'decimal:2',
        'kuota_tenant' => 'integer',
    ];

    public function booths(): HasMany
    {
        return $this->hasMany(Booth::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
