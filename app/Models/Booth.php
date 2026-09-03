<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booth extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'kode_booth',
        'zona',
        'ukuran',
        'harga',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }
}
