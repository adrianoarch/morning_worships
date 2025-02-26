<?php

namespace App\Services;

use App\Models\MorningWorship;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WorshipService
{
    /**
     * Return a paginated list of MorningWorships ordered by first_published descending.
     * If a search query is given, it will be used to filter the results.
     *
     * @param string|null $search
     * @param bool $searchInSubtitles
     * @param bool $watchedOnly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWorships(
        ?string $search = null,
        bool $searchInSubtitles = false,
        bool $watchedOnly = false
    ): LengthAwarePaginator {
        $query = MorningWorship::query();

        if ($watchedOnly) {
            $query->whereHas('watchedByUsers', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        if ($search) {
            if ($searchInSubtitles) {
                $query->whereRaw('MATCH (subtitles_text) AGAINST (? IN BOOLEAN MODE)', [$search]);
            } else {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }
        }

        return $query->orderByDesc('first_published')
            ->paginate(15);
    }


    /**
     * Get the count of MorningWorships watched by the currently authenticated user.
     *
     * @return int
     */
    public function getWatchedWorshipsCount(): int
    {
        return Auth::check()
            ? Auth::user()->watchedWorships()->count()
            : 0;
    }
}
