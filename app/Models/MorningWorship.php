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
        'natural_key',
        'title',
        'description',
        'first_published',
        'duration',
        'duration_formatted',
        'video_url',
        'image_url',
        'subtitles',
        'subtitles_text',
        'watched_at',
    ];

    protected $casts = [
        'first_published' => 'datetime',
        'watched_at' => 'datetime',
        'subtitles' => 'array',
        'natural_key' => 'string',
    ];

    protected $appends = ['video_url_jw'];

    /**
     * Usuários que assistiram esta adoração matinal.
     *
     * @return BelongsToMany<User, MorningWorship>
     */
    public function watchedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_watched_worships')
            ->withTimestamps()
            ->withPivot('watched_at', 'notes');
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

    public function getVideoUrlJwAttribute()
    {
        return "https://www.jw.org/pt/biblioteca/videos/#pt/mediaitems/VODPgmEvtMorningWorship/{$this->natural_key}";
    }
}
