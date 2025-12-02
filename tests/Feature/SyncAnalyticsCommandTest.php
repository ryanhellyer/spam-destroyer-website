<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AnalyticsSyncService;
use Tests\TestCase;

class SyncAnalyticsCommandTest extends TestCase
{
    public function test_analytics_sync_command_runs_successfully(): void
    {
        $mock = $this->mock(AnalyticsSyncService::class);
        $mock->shouldReceive('sync')->once()->andReturn(['synced' => 3, 'errors' => []]);

        $this->artisan('analytics:sync')
            ->expectsOutput('Starting analytics sync...')
            ->expectsOutput('Synced 3 paths')
            ->expectsOutput('Sync completed successfully')
            ->assertExitCode(0);
    }

    public function test_analytics_sync_command_reports_errors(): void
    {
        $mock = $this->mock(AnalyticsSyncService::class);
        $mock->shouldReceive('sync')->once()->andReturn(['synced' => 1, 'errors' => ['Something failed']]);

        $this->artisan('analytics:sync')
            ->expectsOutput('Starting analytics sync...')
            ->expectsOutput('Synced 1 paths')
            ->assertExitCode(1);
    }

    public function test_analytics_sync_command_handles_empty_sync(): void
    {
        $mock = $this->mock(AnalyticsSyncService::class);
        $mock->shouldReceive('sync')->once()->andReturn(['synced' => 0, 'errors' => []]);

        $this->artisan('analytics:sync')
            ->expectsOutput('Starting analytics sync...')
            ->expectsOutput('Synced 0 paths')
            ->expectsOutput('Sync completed successfully')
            ->assertExitCode(0);
    }
}
