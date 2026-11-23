<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'duration',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'duration' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot('id', 'notes')
            ->withTimestamps();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('genre', 'like', "%{$search}%");
                });
            })
            ->when($filters['title'] ?? null, function (Builder $query, string $title) {
                $query->where('title', 'like', "%{$title}%");
            })
            ->when($filters['genre'] ?? null, function (Builder $query, string $genre) {
                $query->where('genre', 'like', "%{$genre}%");
            })
            ->when($filters['type'] ?? null, function (Builder $query, string $type) {
                $query->where('type', $type);
            });
    }
}
