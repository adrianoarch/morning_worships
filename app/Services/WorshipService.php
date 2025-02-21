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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWorships(?string $search = null): LengthAwarePaginator
    {
        return MorningWorship::search($search)
            ->orderByDesc('first_published')
            ->paginate(15);
    }


    /**
     * Get the count of MorningWorships watched by the currently authenticated user.
     *
     * @return int
     */
    public function getWatchedWorshipsCount(): int
    {
        return Auth::user()->watchedWorships()->count();
    }
}
