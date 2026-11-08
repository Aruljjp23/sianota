<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penghadap extends Model
{
    protected $table = 'penghadap';

    protected $primaryKey = 'id_penghadap';

    protected $fillable = [
        'id_skmht',
        'id_legalisasi',
        'nama_penghadap',
    ];

    public function skmht(): BelongsTo
    {
        return $this->belongsTo(
            Skmht::class,
            'id_skmht',
            'id_skmht'
        );
    }

    public function legalisasi(): BelongsTo
    {
        return $this->belongsTo(
            Legalisasi::class,
            'id_legalisasi',
            'id_legalisasi'
        );
    }
}
