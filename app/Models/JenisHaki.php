<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisHaki extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'jenis_hakis';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'status',
    ];

    /**
     * Relasi ke data HAKI (HasMany).
     */
    public function hakis(): HasMany
    {
        return $this->hasMany(Haki::class, 'jenis_haki_id');
    }
}
