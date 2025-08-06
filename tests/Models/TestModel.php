<?php

namespace EnjoySoftware\LaravelHits\Tests\Models;

use EnjoySoftware\LaravelHits\Contracts\HittableInterface;
use EnjoySoftware\LaravelHits\Traits\Hittable;
use Illuminate\Database\Eloquent\Model;

/**
 * Test model for Laravel Hits package
 */
class TestModel extends Model implements HittableInterface
{
    use Hittable;

    protected $table = 'test_models';

    protected $fillable = ['title'];
}
