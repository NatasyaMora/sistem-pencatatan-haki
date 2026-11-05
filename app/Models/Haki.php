<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Haki extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'hakis';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'jenis_haki_id',
        'faculty_id',
        'study_program_id',
        'judul_haki',
        'jenis_haki',
        'nomor_sertifikat',
        'tanggal_terbit',
        'tanggal_kadaluarsa',
        'tahun',
        'deskripsi',
        'file_sertifikat',
        'status',
        'is_public',
    ];

    /**
     * Cast tipe data atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_kadaluarsa' => 'date',
            'tahun' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Relasi ke pemilik HAKI (BelongsTo).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jenis HAKI (BelongsTo).
     */
    public function jenisHaki(): BelongsTo
    {
        return $this->belongsTo(JenisHaki::class, 'jenis_haki_id');
    }

    /**
     * Relasi ke Fakultas (BelongsTo).
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Relasi ke Program Studi (BelongsTo).
     */
    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    /**
     * Relasi ke riwayat pengajuan perpanjangan HAKI (HasMany).
     */
    public function renewalRequests(): HasMany
    {
        return $this->hasMany(RenewalRequest::class);
    }

    /**
     * Scope untuk pencarian judul_haki.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if ($keyword) {
            return $query->where('judul_haki', 'like', '%' . $keyword . '%');
        }

        return $query;
    }

    /**
     * Scope untuk filter jenis_haki.
     */
    public function scopeJenis(Builder $query, ?string $jenis): Builder
    {
        if ($jenis) {
            return $query->where('jenis_haki', $jenis);
        }

        return $query;
    }

    /**
     * Scope untuk filter tahun.
     */
    public function scopeTahun(Builder $query, ?int $tahun): Builder
    {
        if ($tahun) {
            return $query->where('tahun', $tahun);
        }

        return $query;
    }

    /**
     * Scope untuk filter status is_public.
     */
    public function scopePublic(Builder $query, bool $isPublic = true): Builder
    {
        return $query->where('is_public', $isPublic);
    }

    /**
     * Sinkronisasi status HAKI yang sudah melewati tanggal kedaluwarsa
     * atau tidak memiliki tanggal kedaluwarsa.
     */
    public static function syncExpiredStatuses(): int
    {
        $today = now('Asia/Jakarta')->toDateString();

        return static::query()
            ->where(function (Builder $query) {
                $query->where('status', 'aktif')
                    ->orWhere('is_public', true);
            })
            ->where(function (Builder $query) use ($today) {
                $query->whereNull('tanggal_kadaluarsa')
                    ->orWhereDate('tanggal_kadaluarsa', '<', $today);
            })
            ->update([
                'status' => 'nonaktif',
                'is_public' => false,
            ]);
    }
}