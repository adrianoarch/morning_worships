<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\UserWatchedWorship;

class MorningWorship extends Model
{
    protected $fillable = [
        'guid',
        'title',
        'description',
        'first_published',
        'duration',
        'duration_formatted',
        'video_url',
        'image_url',
        'subtitles',
        'subtitles_text',
        'watched_at'
    ];

    protected $casts = [
        'first_published' => 'datetime',
        'watched_at' => 'datetime',
        'subtitles' => 'array'
    ];

    /**
     * Retorna a relação com os usuários que assistiram a essa adoração matinal.
     *
     * @return BelongsToMany
     */
    public function watchedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_watched_worships')
            ->withPivot('watched_at')
            ->withTimestamps();
    }

    // Método helper para verificar se um usuário específico já assistiu
    public function wasWatchedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->watchedByUsers()->where('user_id', $user->id)->exists();
    }

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where('title', 'like', "%{$search}%");
        }

        return $query;
    }
}
