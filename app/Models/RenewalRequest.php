<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalRequest extends Model
{
    protected $fillable = [
        'haki_id',
        'user_id',
        'tanggal_kadaluarsa_baru',
        'nomor_sertifikat_baru',
        'file_sertifikat_baru',
        'catatan',
        'status',
        'catatan_admin',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal_kadaluarsa_baru' => 'date',
        'diproses_pada' => 'datetime',
    ];

    public function haki(): BelongsTo
    {
        return $this->belongsTo(Haki::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}