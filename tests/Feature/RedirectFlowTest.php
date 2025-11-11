<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\UrlMapping;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedirectFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_redirects_to_checking_for_valid_slug(): void
    {
        $mapping = UrlMapping::factory()->create(['slug' => 'abc123', 'url' => 'https://example.com']);

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/check/'.$mapping->slug);

        $response->assertStatus(302);
        $response->assertRedirect(route('checking', ['slug' => $mapping->slug]));
    }

    public function test_check_returns_404_for_nonexistent_slug(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/check/nonexistent');

        $response->assertStatus(404);
    }

    public function test_checking_returns_ok_with_valid_slug(): void
    {
        UrlMapping::factory()->create(['slug' => 'abc123', 'url' => 'https://example.com']);
        Cache::flush();

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/checking/abc123');

        $response->assertStatus(200);
        $response->assertSee('https://example.com');
    }

    public function test_checking_returns_404_for_nonexistent_slug(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/checking/nonexistent');

        $response->assertStatus(404);
    }
}
