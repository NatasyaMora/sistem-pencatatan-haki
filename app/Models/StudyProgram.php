<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'study_programs';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'faculty_id',
        'nama',
        'status',
    ];

    /**
     * Relasi ke Fakultas (BelongsTo).
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Relasi ke data HAKI (HasMany).
     */
    public function hakis(): HasMany
    {
        return $this->hasMany(Haki::class, 'study_program_id');
    }
}
