<?php

namespace App\Services;

use App\Models\MorningWorship;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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
        $query = $this->buildFilteredQuery($search, $searchInSubtitles, $watchedOnly);

        return $query
            ->orderByDesc('first_published')
            ->orderByDesc('id')
            ->paginate(15);
    }

    /**
     * Build a filtered query for MorningWorship based on provided filters.
     */
    public function buildFilteredQuery(
        ?string $search = null,
        bool $searchInSubtitles = false,
        bool $watchedOnly = false
    ): Builder {
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
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }

    /**
     * Get the next worship id based on current filters and current worship, using order by first_published desc, id desc.
     */
    public function getNextWorshipId(
        MorningWorship $current,
        ?string $search = null,
        bool $searchInSubtitles = false,
        bool $watchedOnly = false
    ): ?int {
        $query = $this->buildFilteredQuery($search, $searchInSubtitles, $watchedOnly);

        // In desc order, the "next" is the one with first_published < current->first_published,
        // or same date and id < current id (tiebreaker).
        $next = $query
            ->where(function ($q) use ($current) {
                $q->where('first_published', '<', $current->first_published)
                  ->orWhere(function ($q2) use ($current) {
                      $q2->where('first_published', $current->first_published)
                         ->where('id', '<', $current->id);
                  });
            })
            ->orderByDesc('first_published')
            ->orderByDesc('id')
            ->first(['id']);

        return $next?->id;
    }

    /**
     * Get the first worship id for the current filters using the same ordering as listing.
     */
    public function getFirstWorshipId(
        ?string $search = null,
        bool $searchInSubtitles = false,
        bool $watchedOnly = false
    ): ?int {
        $query = $this->buildFilteredQuery($search, $searchInSubtitles, $watchedOnly);

        $first = $query
            ->orderByDesc('first_published')
            ->orderByDesc('id')
            ->first(['id']);

        return $first?->id;
    }


    /**
     * Get the count of MorningWorships watched by the currently authenticated user.
     *
     * @return int
     */
    public function getWatchedWorshipsCount(): int
    {
        return Auth::user()?->watchedWorships()->count() ?? 0;
    }
}
