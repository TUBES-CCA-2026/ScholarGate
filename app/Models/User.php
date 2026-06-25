<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model pengguna ScholarGate.
 *
 * Role yang digunakan aplikasi hanya admin dan student. Admin mengelola master
 * dan review, sedangkan student membuat pengajuan dan menyimpan bookmark.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STUDENT = 'student';

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim',
        'program_studi',
        'kelas',
        'ipk',
        'phone',
        'photo_path',
    ];

    /**
     * Atribut sensitif yang disembunyikan saat model dikonversi ke array/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ipk' => 'decimal:2',
        ];
    }

    /**
     * Pengajuan yang dimiliki mahasiswa.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(StudentApplication::class);
    }

    /**
     * Bookmark yang disimpan pengguna.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Master beasiswa yang disimpan pengguna melalui bookmark.
     */
    public function bookmarkedDocumentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'bookmarks')->withTimestamps();
    }

    /**
     * Memeriksa apakah pengguna aktif memiliki role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Memeriksa apakah pengguna aktif memiliki role student.
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }
}
