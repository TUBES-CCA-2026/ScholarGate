<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentApplication extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_REVISION = 'revision';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Dikirim',
        self::STATUS_IN_REVIEW => 'Sedang Direview',
        self::STATUS_REVISION => 'Perlu Revisi',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    protected $fillable = [
        'user_id',
        'document_type_id',
        'application_code',
        'purpose',
        'status',
        'admin_note',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? str($this->status)->replace('_', ' ')->title()->toString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

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
