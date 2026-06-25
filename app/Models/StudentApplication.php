<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model header pengajuan mahasiswa.
 */
class StudentApplication extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_REVISION = 'revision';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Label status utama pengajuan yang ditampilkan di UI.
     */
    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Dikirim',
        self::STATUS_IN_REVIEW => 'Sedang Direview',
        self::STATUS_REVISION => 'Perlu Revisi',
        self::STATUS_APPROVED => 'Siap Diambil',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'user_id',
        'document_type_id',
        'application_code',
        'purpose',
        'status',
        'admin_note',
        'submitted_at',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
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
     * Mahasiswa pemilik pengajuan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Master beasiswa yang diajukan.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Detail dokumen yang dilampirkan pada pengajuan ini.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    /**
     * Menghitung persentase kelengkapan dokumen pengajuan.
     */
    public function completionPercentage(): int
    {
        $total = $this->documents->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->documents
            ->filter(fn (ApplicationDocument $document): bool => in_array($document->status, [
                ApplicationDocument::STATUS_SUBMITTED,
                ApplicationDocument::STATUS_VALID,
            ], true))
            ->count();

        return (int) round(($completed / $total) * 100);
    }
}
