<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill any existing null admin_hash values (safety net)
        $urlMappings = DB::table('url_mappings')->whereNull('admin_hash')->get();

        foreach ($urlMappings as $mapping) {
            $adminHash = hash_hmac('sha256', $mapping->slug, config('app.key'));
            DB::table('url_mappings')
                ->where('id', $mapping->id)
                ->update(['admin_hash' => $adminHash]);
        }

        Schema::table('url_mappings', function (Blueprint $table) {
            $table->string('admin_hash')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('url_mappings', function (Blueprint $table) {
            $table->string('admin_hash')->nullable()->change();
        });
    }
};
