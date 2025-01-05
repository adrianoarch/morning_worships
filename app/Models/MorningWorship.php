<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'watched_at'
    ];

    protected $casts = [
        'first_published' => 'datetime',
        'watched_at' => 'datetime',
        'subtitles' => 'array'
    ];

    public function viewedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_worship_views')
                    ->withTimestamps()
                    ->withPivot('watched_at', 'notes');
    }

    // Método helper para verificar se um usuário específico já assistiu
    public function wasWatchedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->viewedByUsers()->where('user_id', $user->id)->exists();
    }
}
