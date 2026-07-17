<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model pengumuman yang dipublikasikan admin untuk mahasiswa.
 */
class Announcement extends Model
{
    use HasFactory, HasUuids;

    /**
     * Atribut yang boleh diisi melalui mass assignment.
     */
    protected $fillable = [
        'title',
        'body',
        'published_at',
    ];

    /**
     * Konversi tipe data atribut model.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
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