<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model syarat dokumen untuk satu master beasiswa.
 */
class Requirement extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'document_type_id',
        'name',
        'description',
        'is_required',
        'needs_file',
        'has_expiry',
        'valid_days',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'needs_file' => 'boolean',
            'has_expiry' => 'boolean',
        ];
    }

    /**
     * Master beasiswa pemilik requirement ini.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Dokumen pengajuan yang mengacu pada requirement ini.
     */
    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
}
