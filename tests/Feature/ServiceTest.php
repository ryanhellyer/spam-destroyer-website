<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AnalyticsConstants;
use App\Services\AnalyticsService;
use App\Services\UrlLookupService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function test_analytics_service_track_hit_calls_redis(): void
    {
        Redis::shouldReceive('incr')
            ->with(AnalyticsConstants::REDIS_PREFIX.'/check/abc')
            ->once()
            ->andReturn(1);

        $service = $this->app->make(AnalyticsService::class);
        $service->trackHit('/check/abc');
    }

    public function test_analytics_service_track_hit_logs_error_on_exception(): void
    {
        Redis::shouldReceive('incr')
            ->andThrow(new \RuntimeException('Connection refused'));

        Log::shouldReceive('error')->once();

        $service = $this->app->make(AnalyticsService::class);
        $service->trackHit('/check/abc');
    }

    public function test_analytics_service_get_hit_count_returns_value_from_redis(): void
    {
        Redis::shouldReceive('get')
            ->with(AnalyticsConstants::REDIS_PREFIX.'/check/abc')
            ->andReturn('42');

        $service = $this->app->make(AnalyticsService::class);
        $result = $service->getHitCount('/check/abc');

        $this->assertSame(42, $result);
    }

    public function test_analytics_service_get_hit_count_returns_zero_when_key_not_found(): void
    {
        Redis::shouldReceive('get')
            ->with(AnalyticsConstants::REDIS_PREFIX.'/check/abc')
            ->andReturn(null);

        $service = $this->app->make(AnalyticsService::class);
        $result = $service->getHitCount('/check/abc');

        $this->assertSame(0, $result);
    }

    public function test_analytics_service_get_hit_count_returns_zero_on_exception(): void
    {
        Redis::shouldReceive('get')
            ->andThrow(new \RuntimeException('Connection refused'));

        Log::shouldReceive('error')->once();

        $service = $this->app->make(AnalyticsService::class);
        $result = $service->getHitCount('/check/abc');

        $this->assertSame(0, $result);
    }

    public function test_url_lookup_service_get_url_returns_url_from_cache(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn('https://example.com');

        $service = $this->app->make(UrlLookupService::class);
        $result = $service->getUrl('abc123');

        $this->assertSame('https://example.com', $result);
    }

    public function test_url_lookup_service_slug_exists_returns_bool_from_cache(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(true);

        $service = $this->app->make(UrlLookupService::class);
        $result = $service->slugExists('abc123');

        $this->assertTrue($result);
    }

    public function test_url_lookup_service_clear_cache_forgets_keys(): void
    {
        $slug = 'abc123';

        Cache::shouldReceive('forget')->with('url_mapping:'.$slug)->once()->andReturnTrue();
        Cache::shouldReceive('forget')->with('url_mapping:'.$slug.':exists')->once()->andReturnTrue();
        Cache::shouldReceive('forget')->with('url_mappings:all')->once()->andReturnTrue();

        $service = $this->app->make(UrlLookupService::class);
        $service->clearCache($slug);
    }

    public function test_url_lookup_service_clear_all_cache_forgets_all_key(): void
    {
        Cache::shouldReceive('forget')
            ->with('url_mappings:all')
            ->once()
            ->andReturnTrue();

        $service = $this->app->make(UrlLookupService::class);
        $service->clearAllCache();
    }
}
