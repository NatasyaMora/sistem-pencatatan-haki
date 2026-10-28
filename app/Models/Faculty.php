<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'faculties';

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
     * Relasi ke Program Studi (HasMany).
     */
    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class, 'faculty_id');
    }

    /**
     * Relasi ke data HAKI (HasMany).
     */
    public function hakis(): HasMany
    {
        return $this->hasMany(Haki::class, 'faculty_id');
    }
}
