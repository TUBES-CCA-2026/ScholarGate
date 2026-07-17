<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model master jenis pengajuan atau beasiswa.
 */
class DocumentType extends Model
{
    use HasFactory, HasUuids;

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'name',
        'category',
        'provider',
        'description',
        'image_path',
        'deadline',
        'registration_link',
        'is_active',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Syarat berkas yang melekat pada master ini.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    /**
     * Pengajuan mahasiswa yang menggunakan master ini.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(StudentApplication::class);
    }

    /**
     * Bookmark yang menunjuk ke master ini.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Pengguna yang menyimpan master ini sebagai bookmark.
     */
    public function bookmarkedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
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
