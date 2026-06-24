<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;

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

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(StudentApplication::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }
}
