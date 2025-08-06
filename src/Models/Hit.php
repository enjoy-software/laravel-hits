<?php

namespace EnjoySoftware\LaravelHits\Models;

use Carbon\Exceptions\InvalidFormatException;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * Hit model for tracking hits on various models.
 *
 * @property-read Model|\Eloquent $hittable
 *
 * @method static Builder<static>|Hit betweenDates(\Illuminate\Support\Carbon|\DateTime|string $startDate, \Illuminate\Support\Carbon|\DateTime|string $endDate)
 * @method static Builder<static>|Hit fromIp(string $ip)
 * @method static Builder<static>|Hit fromModel(\Illuminate\Database\Eloquent\Model $model)
 * @method static Builder<static>|Hit fromUser(int $userId)
 * @method static Builder<static>|Hit lastDays(int $days)
 * @method static Builder<static>|Hit newModelQuery()
 * @method static Builder<static>|Hit newQuery()
 * @method static Builder<static>|Hit query()
 * @method static Builder<static>|Hit thisMonth()
 * @method static Builder<static>|Hit thisWeek()
 * @method static Builder<static>|Hit today()
 *
 * @mixin \Eloquent
 */
class Hit extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'hittable_id',
        'hittable_type',
        'ip',
        'method',
        'referer',
        'url',
        'user_agent',
        'user_id',
    ];

    /**
     * Get the parent hittable model.
     */
    public function hittable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the hit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Scope a query to only include hits between two dates.
     *
     * @throws InvalidFormatException
     * @throws InvalidArgumentException
     */
    public function scopeBetweenDates(
        Builder $query,
        string|Carbon|DateTime $startDate,
        string|Carbon|DateTime $endDate
    ): void {
        $start = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $end = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        if ($start->gt($end)) {
            throw new InvalidArgumentException(
                'Start date must be before or equal to end date'
            );
        }

        $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope a query to only include hits from a specific IP.
     */
    public function scopeFromIp(Builder $query, string $ip): void
    {
        $query->where('ip', $ip);
    }

    /**
     * Scope a query to only include hits from a specific model.
     */
    public function scopeFromModel(Builder $query, Model $model): void
    {
        $query->where('hittable_type', $model->getMorphClass())
            ->where('hittable_id', $model->getKey());
    }

    /**
     * Scope a query to only include hits from a specific user.
     */
    public function scopeFromUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include this month's hits.
     */
    public function scopeThisMonth(Builder $query): void
    {
        $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    /**
     * Scope a query to only include this week's hits.
     */
    public function scopeThisWeek(Builder $query): void
    {
        $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope a query to only include today's hits.
     */
    public function scopeToday(Builder $query): void
    {
        $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include hits within the last N days.
     */
    public function scopeLastDays(Builder $query, int $days): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }
}
