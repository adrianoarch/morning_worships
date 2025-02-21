<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWatchedWorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'morning_worship_id',
        'watched_at',
        'notes',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function worship() : BelongsTo
    {
        return $this->belongsTo(MorningWorship::class);
    }
}
