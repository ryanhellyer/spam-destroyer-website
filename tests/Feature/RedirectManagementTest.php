<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\UrlMapping;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_redirect_and_redirects_to_admin(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->post('/', ['to' => 'https://example.com']);

        $response->assertStatus(302);
        $this->assertDatabaseHas('url_mappings', ['url' => 'https://example.com']);
    }

    public function test_store_requires_valid_url(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->post('/', ['to' => 'not-a-url']);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('to');
    }

    public function test_store_requires_to_field(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->post('/', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('to');
    }

    public function test_admin_show_displays_redirect_details(): void
    {
        $mapping = UrlMapping::factory()->create(['url' => 'https://example.com']);

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
            $mock->shouldReceive('getHitCount')->andReturn(0);
        });

        $response = $this->get('/admin/'.$mapping->admin_hash);

        $response->assertStatus(200);
        $response->assertSee($mapping->slug);
    }

    public function test_admin_show_returns_404_for_invalid_hash(): void
    {
        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->get('/admin/nonexistent-hash');

        $response->assertStatus(404);
    }

    public function test_update_changes_slug_and_url(): void
    {
        $mapping = UrlMapping::factory()->create(['slug' => 'oldslug', 'url' => 'https://old.com']);

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->put('/admin/'.$mapping->admin_hash, [
            'from' => 'newslug',
            'to' => 'https://new.com',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('url_mappings', [
            'id' => $mapping->id,
            'slug' => 'newslug',
            'url' => 'https://new.com',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $mapping = UrlMapping::factory()->create();

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->put('/admin/'.$mapping->admin_hash, []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['from', 'to']);
    }

    public function test_update_rejects_invalid_url(): void
    {
        $mapping = UrlMapping::factory()->create();

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->put('/admin/'.$mapping->admin_hash, [
            'from' => 'valid-slug',
            'to' => 'not-a-url',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('to');
    }

    public function test_update_accepts_optional_email(): void
    {
        $mapping = UrlMapping::factory()->create();

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->put('/admin/'.$mapping->admin_hash, [
            'from' => 'valid-slug',
            'to' => 'https://example.com',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('url_mappings', [
            'id' => $mapping->id,
            'email' => 'test@example.com',
        ]);
    }

    public function test_update_rejects_invalid_email(): void
    {
        $mapping = UrlMapping::factory()->create();

        $this->mock(AnalyticsService::class, function ($mock) {
            $mock->shouldReceive('trackHit')->andReturn(null);
        });

        $response = $this->put('/admin/'.$mapping->admin_hash, [
            'from' => 'valid-slug',
            'to' => 'https://example.com',
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }
}
