<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\AnalyticsSyncService;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class AnalyticsSyncServiceTest extends TestCase
{
    public function test_extract_slug_from_check_path_with_leading_slash(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'extractSlugFromCheckPath');

        $result = $method->invoke($service, '/check/my-slug');

        $this->assertSame('my-slug', $result);
    }

    public function test_extract_slug_from_check_path_without_leading_slash(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'extractSlugFromCheckPath');

        $result = $method->invoke($service, 'check/my-slug');

        $this->assertSame('my-slug', $result);
    }

    public function test_extract_slug_returns_null_for_non_check_path(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'extractSlugFromCheckPath');

        $this->assertNull($method->invoke($service, '/about'));
        $this->assertNull($method->invoke($service, '404'));
        $this->assertNull($method->invoke($service, '/admin/hash123'));
    }

    public function test_normalize_path_for_storage_returns_404_as_is(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'normalizePathForStorage');

        $result = $method->invoke($service, '404');

        $this->assertSame('404', $result);
    }

    public function test_normalize_path_for_storage_adds_leading_slash(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'normalizePathForStorage');

        $result = $method->invoke($service, 'about');

        $this->assertSame('/about', $result);
    }

    public function test_normalize_path_for_storage_preserves_paths_with_leading_slash(): void
    {
        $service = new AnalyticsSyncService;
        $method = new ReflectionMethod($service, 'normalizePathForStorage');

        $result = $method->invoke($service, '/about');

        $this->assertSame('/about', $result);
    }

    public function test_sync_returns_empty_result_when_no_keys(): void
    {
        Redis::shouldReceive('keys')->once()->andReturn([]);

        $service = new AnalyticsSyncService;
        $result = $service->sync();

        $this->assertSame(['synced' => 0, 'errors' => []], $result);
    }

    public function test_sync_returns_empty_result_when_redis_keys_is_not_array(): void
    {
        Redis::shouldReceive('keys')->once()->andReturn(null);

        $service = new AnalyticsSyncService;
        $result = $service->sync();

        $this->assertSame(['synced' => 0, 'errors' => []], $result);
    }
}
