<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    public const STATUS_MISSING = 'missing';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VALID = 'valid';
    public const STATUS_INVALID = 'invalid';

    public const STATUS_LABELS = [
        self::STATUS_MISSING => 'Belum Ada',
        self::STATUS_SUBMITTED => 'Dikirim',
        self::STATUS_VALID => 'Valid',
        self::STATUS_INVALID => 'Perlu Revisi',
    ];

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

    protected function casts(): array
    {
        return [
            'is_checked_manual' => 'boolean',
            'expired_at' => 'date',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? str($this->status)->replace('_', ' ')->title()->toString();
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }
}
