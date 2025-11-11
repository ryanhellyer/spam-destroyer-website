<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\UrlLookupService;
use PHPUnit\Framework\TestCase;

class UrlLookupServiceTest extends TestCase
{
    public function test_cache_prefix_constant(): void
    {
        $ref = new \ReflectionClass(UrlLookupService::class);
        $prefix = $ref->getConstant('CACHE_PREFIX');

        $this->assertSame('url_mapping:', $prefix);
    }

    public function test_cache_all_key_constant(): void
    {
        $ref = new \ReflectionClass(UrlLookupService::class);
        $key = $ref->getConstant('CACHE_ALL_KEY');

        $this->assertSame('url_mappings:all', $key);
    }

    public function test_cache_ttl_constant(): void
    {
        $ref = new \ReflectionClass(UrlLookupService::class);
        $ttl = $ref->getConstant('CACHE_TTL');

        $this->assertSame(3600, $ttl);
    }
}
