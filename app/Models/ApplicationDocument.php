<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model detail dokumen yang dilampirkan pada satu pengajuan mahasiswa.
 */
class ApplicationDocument extends Model
{
    use HasFactory;

    public const STATUS_MISSING = 'missing';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VALID = 'valid';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_READY = 'ready';

    /**
     * Label status yang dipakai pada tampilan mahasiswa dan admin.
     */
    public const STATUS_LABELS = [
        self::STATUS_MISSING => 'Belum Siap',
        self::STATUS_SUBMITTED => 'Dikirim',
        self::STATUS_VALID => 'Siap',
        self::STATUS_INVALID => 'Perlu Revisi',
        self::STATUS_READY => 'Siap Diambil',
    ];

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'student_application_id',
        'requirement_id',
        'file_path',
        'original_name',
        'is_checked_manual',
        'expired_at',
        'status',
        'note',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'is_checked_manual' => 'boolean',
            'expired_at' => 'date',
        ];
    }

    /**
     * Accessor untuk label status yang mudah dibaca pada Blade.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? str($this->status)->replace('_', ' ')->title()->toString();
    }

    /**
     * Relasi many-to-one menuju header pengajuan mahasiswa.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }

    /**
     * Relasi many-to-one menuju syarat master beasiswa.
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }
}
