<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_offline')->default(false)->after('status');
            $table->string('sync_token')->nullable()->unique()->after('is_offline');
            $table->timestamp('synced_at')->nullable()->after('sync_token');
            $table->text('sync_error')->nullable()->after('synced_at');
            $table->integer('sync_attempts')->default(0)->after('sync_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_offline', 'sync_token', 'synced_at', 'sync_error', 'sync_attempts']);
        });
    }
};
