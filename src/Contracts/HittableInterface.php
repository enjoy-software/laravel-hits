<?php

namespace EnjoySoftware\LaravelHits\Contracts;

use EnjoySoftware\LaravelHits\Models\Hit;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface for models that can track hits.
 * 
 * Methods are provided by the Hittable trait, or you can implement them yourself.
 */
interface HittableInterface
{
    /**
     * Get the total hits count.
     */
    public function getHitsCount(): int;

    /**
     * Get this month's hits count.
     */
    public function getThisMonthHitsCount(): int;

    /**
     * Get this week's hits count.
     */
    public function getThisWeekHitsCount(): int;

    /**
     * Get today's hits count.
     */
    public function getTodayHitsCount(): int;

    /**
     * Get unique visitors count.
     */
    public function getUniqueVisitorsCount(): int;

    /**
     * Get all hits for the model.
     */
    public function hits(): MorphMany;

    /**
     * Record a hit for the model.
     */
    public function recordHit(array $data = []): ?Hit;
}
