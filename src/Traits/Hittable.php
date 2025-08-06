<?php

namespace EnjoySoftware\LaravelHits\Traits;

use EnjoySoftware\LaravelHits\Contracts\HittableInterface;
use EnjoySoftware\LaravelHits\Models\Hit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for tracking hits on Eloquent models.
 *
 * This trait should be used on Eloquent Model classes that extend
 * Illuminate\Database\Eloquent\Model to enable hit tracking functionality.
 *
 * @implements HittableInterface
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> withCount($relations)
 */
trait Hittable
{
    /**
     * Create the actual hit record.
     */
    private function createHitRecord(array $data): Hit
    {
        return $this->hits()->create(array_merge([
            'ip' => request()->ip(),
            'method' => request()->method(),
            'referer' => request()->header('referer'),
            'url' => request()->fullUrl(),
            'user_agent' => request()->header('User-Agent'),
            'user_id' => Auth::id(),
        ], $data));
    }

    /**
     * Check if we should skip recording due to bot detection.
     */
    protected function shouldSkipBotHit(): bool
    {
        if (!config('laravel-hits.ignore_bots', true)) {
            return false;
        }

        $userAgent = request()->header('User-Agent', '');
        $botPatterns = config(
            'laravel-hits.bot_user_agents',
            [
                'bingbot',
                'bot',
                'crawler',
                'facebookexternalhit',
                'googlebot',
                'linkedinbot',
                'scraper',
                'slackbot',
                'spider',
                'telegrambot',
                'twitterbot',
                'whatsapp',
            ]
        );
        $lowercaseUserAgent = strtolower($userAgent);

        foreach ($botPatterns as $pattern) {
            if (str_contains($lowercaseUserAgent, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if we should skip recording due to cooldown.
     */
    protected function shouldSkipCooldownHit(): bool
    {
        $cooldownMinutes = config('laravel-hits.cooldown_minutes', 5);

        if ($cooldownMinutes <= 0) {
            return false;
        }

        return $this->hits()
            ->where('ip', request()->ip())
            ->where('created_at', '>=', now()->subMinutes($cooldownMinutes))
            ->exists();
    }

    /**
     * Get the total number of hits.
     */
    public function getHitsCount(): int
    {
        return $this->hits()->count();
    }

    /**
     * Get popular models ordered by hit count.
     *
     * @param int $limit Maximum number of models to return
     * @return Collection<int, static> Collection of models with hits_count attribute
     */
    public static function getPopular(int $limit = 10): Collection
    {
        return static::withCount('hits')
            ->orderBy('hits_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get hits count for this month.
     */
    public function getThisMonthHitsCount(): int
    {
        return $this->hits()->thisMonth()->count();
    }

    /**
     * Get hits count for this week.
     */
    public function getThisWeekHitsCount(): int
    {
        return $this->hits()->thisWeek()->count();
    }

    /**
     * Get hits count for today.
     */
    public function getTodayHitsCount(): int
    {
        return $this->hits()->today()->count();
    }

    /**
     * Get trending models within a specific time period.
     *
     * @param int $days Number of days to look back for trending calculation
     * @param int $limit Maximum number of models to return
     * @return Collection<int, static> Collection of models with hits_count attribute
     */
    public static function getTrending(int $days = 7, int $limit = 10): Collection
    {
        $startDate = now()->subDays($days);

        return static::withCount([
            'hits' => function (Builder $query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            },
        ])
            ->orderBy('hits_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unique visitors count.
     */
    public function getUniqueVisitorsCount(): int
    {
        return $this->hits()->distinct('ip')->count('ip');
    }

    /**
     * Check if the model has been hit by a specific IP.
     */
    public function hasBeenHitBy(string $ipAddress): bool
    {
        return $this->hits()->where('ip', $ipAddress)->exists();
    }

    /**
     * Get all of the hits for the model.
     *
     * @return MorphMany|Hit
     */
    public function hits(): MorphMany
    {
        return $this->morphMany(Hit::class, 'hittable');
    }

    /**
     * Record a hit for the model.
     */
    public function recordHit(array $data = []): ?Hit
    {
        // Bot detection
        if ($this->shouldSkipBotHit()) {
            return null;
        }

        // Cooldown check
        if ($this->shouldSkipCooldownHit()) {
            return null;
        }

        return $this->createHitRecord($data);
    }

    /**
     * Record a hit without any validation (raw recording).
     */
    public function recordHitRaw(array $data = []): Hit
    {
        return $this->createHitRecord($data);
    }
}
