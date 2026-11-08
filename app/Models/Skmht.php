<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skmht extends Model
{
    protected $table = 'skmht';
    protected $primaryKey = 'id_skmht';

    protected $fillable = [
        'nomor_bulanan',
        'tanggal_akta',
        'sifat_jenis_akta',
        'alamat',
        'jam',
    ];

    protected $casts = [
        'tanggal_akta' => 'date',
    ];

    public function penghadap()
    {
        return $this->hasMany(
            Penghadap::class,
            'id_skmht',
            'id_skmht'
        );
    }
}