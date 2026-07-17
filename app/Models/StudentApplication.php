<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Model header pengajuan mahasiswa.
 */
class StudentApplication extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_REVISION = 'revision';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_READY = 'ready_pickup';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Label status utama pengajuan yang ditampilkan di UI.
     */
    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Diajukan',
        self::STATUS_IN_REVIEW => 'Diproses',
        self::STATUS_REVISION => 'Diproses',
        self::STATUS_READY => 'Siap Diambil',
        self::STATUS_COMPLETED => 'Siap Diambil',
        self::STATUS_APPROVED => 'Siap Diambil',
        self::STATUS_REJECTED => 'Ditolak',
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
     * Mengambil daftar document_type_id yang sudah pernah diajukan oleh mahasiswa.
     * Digunakan untuk mencegah pengajuan beasiswa yang sama dua kali.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public static function appliedDocumentTypeIds(int $userId): \Illuminate\Support\Collection
    {
        return static::where('user_id', $userId)
            ->pluck('document_type_id');
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
            ->filter(
                fn(ApplicationDocument $document): bool =>
                in_array($document->status, [
                    ApplicationDocument::STATUS_VALID,
                    ApplicationDocument::STATUS_READY,
                ])
            )
            ->count();

        return (int) round(($completed / $total) * 100);
    }

        /**
     * Kolom yang otomatis diisi UUID v7 oleh Eloquent.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uid'];
    }

    /**
     * Gunakan uid pada URL dan implicit route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }
}

