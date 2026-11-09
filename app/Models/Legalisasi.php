<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Legalisasi extends Model
{
    protected $table = 'legalisasi';

    protected $primaryKey = 'id_legalisasi';

    protected $fillable = [
        'no_urut',
        'tanggal',
        'sifat_surat',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penghadap(): HasMany
    {
        return $this->hasMany(
            Penghadap::class,
            'id_legalisasi',
            'id_legalisasi'
        );
    }
}