<?php

namespace EnjoySoftware\LaravelHits\Tests\Feature;

use EnjoySoftware\LaravelHits\Models\Hit;
use EnjoySoftware\LaravelHits\Tests\Models\TestModel;
use EnjoySoftware\LaravelHits\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class HittableTraitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 創建測試模型的資料表
        Schema::create('test_models', function ($table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function test_it_can_record_a_hit()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        $hit = $model->recordHit([
            'ip' => '192.168.1.1',
            'user_agent' => 'Test Browser',
        ]);

        $this->assertInstanceOf(Hit::class, $hit);
        $this->assertEquals('192.168.1.1', $hit->ip);
        $this->assertEquals('Test Browser', $hit->user_agent);
        $this->assertEquals($model->id, $hit->hittable_id);
        $this->assertEquals(TestModel::class, $hit->hittable_type);
    }

    public function test_it_can_get_hits_count()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        $this->assertEquals(0, $model->getHitsCount());

        $model->recordHit(['ip' => '192.168.1.1']);
        $model->recordHit(['ip' => '192.168.1.2']);

        $this->assertEquals(2, $model->getHitsCount());
    }

    public function test_it_can_get_unique_visitors_count()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        $model->recordHit(['ip' => '192.168.1.1']);
        $model->recordHit(['ip' => '192.168.1.1']); // duplicate IP
        $model->recordHit(['ip' => '192.168.1.2']);

        $this->assertEquals(2, $model->getUniqueVisitorsCount());
    }

    public function test_it_can_check_if_hit_by_ip()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        $this->assertFalse($model->hasBeenHitBy('192.168.1.1'));

        $model->recordHit(['ip' => '192.168.1.1']);

        $this->assertTrue($model->hasBeenHitBy('192.168.1.1'));
        $this->assertFalse($model->hasBeenHitBy('192.168.1.2'));
    }

    public function test_it_can_get_popular_models()
    {
        $model1 = TestModel::create(['title' => 'Model 1']);
        $model2 = TestModel::create(['title' => 'Model 2']);
        $model3 = TestModel::create(['title' => 'Model 3']);

        // Model 2 get the most clicks
        $model2->recordHit(['ip' => '192.168.1.1']);
        $model2->recordHit(['ip' => '192.168.1.2']);
        $model2->recordHit(['ip' => '192.168.1.3']);

        // Model 1 get a click
        $model1->recordHit(['ip' => '192.168.1.1']);

        // Model 3 no clicks

        $popular = TestModel::getPopular(3);

        $this->assertCount(3, $popular);
        $this->assertEquals($model2->id, $popular[0]->id);
        $this->assertEquals($model1->id, $popular[1]->id);
        $this->assertEquals($model3->id, $popular[2]->id);
    }

    public function test_it_can_get_trending_models()
    {
        $model1 = TestModel::create(['title' => 'Model 1']);
        $model2 = TestModel::create(['title' => 'Model 2']);

        // Create recent hits for model2
        $model2->recordHit(['ip' => '192.168.1.1']);
        $model2->recordHit(['ip' => '192.168.1.2']);

        // Create fewer hits for model1
        $model1->recordHit(['ip' => '192.168.1.3']);

        $trending = TestModel::getTrending(7, 2);

        $this->assertCount(2, $trending);
        $this->assertEquals($model2->id, $trending[0]->id);
        $this->assertEquals($model1->id, $trending[1]->id);
    }

    public function test_it_can_get_time_based_hit_counts()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        // record some hits
        $model->recordHit(['ip' => '192.168.1.1']);
        $model->recordHit(['ip' => '192.168.1.2']);

        $this->assertEquals(2, $model->getTodayHitsCount());
        $this->assertEquals(2, $model->getThisWeekHitsCount());
        $this->assertEquals(2, $model->getThisMonthHitsCount());
    }

    public function test_it_skips_recording_when_cooldown_is_active()
    {
        $model = TestModel::create(['title' => 'Test Model']);

        // Set a short cooldown time
        config(['laravel-hits.cooldown_minutes' => 1]);

        // Simulate request IP
        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.1');

        // Record first hit
        $hit1 = $model->recordHit();
        $this->assertInstanceOf(Hit::class, $hit1);

        // Immediately record again (should be skipped)
        $hit2 = $model->recordHit();
        $this->assertNull($hit2);

        // Verify only one hit was recorded
        $this->assertEquals(1, $model->getHitsCount());
    }
}
