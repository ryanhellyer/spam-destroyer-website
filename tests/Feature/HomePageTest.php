<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AnalyticsService;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_returns_ok(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_legal_notice_page_returns_ok(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/legal-notice/');

        $response->assertStatus(200);
    }

    public function test_privacy_policy_page_returns_ok(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/privacy-policy/');

        $response->assertStatus(200);
    }
}
