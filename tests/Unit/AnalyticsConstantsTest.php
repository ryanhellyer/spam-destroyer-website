<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\AnalyticsConstants;
use PHPUnit\Framework\TestCase;

class AnalyticsConstantsTest extends TestCase
{
    public function test_redis_prefix_value(): void
    {
        $this->assertSame('analytics:hit:', AnalyticsConstants::REDIS_PREFIX);
    }

    public function test_redis_prefix_ends_with_colon(): void
    {
        $this->assertStringEndsWith(':', AnalyticsConstants::REDIS_PREFIX);
    }
}
