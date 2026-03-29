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
            $table->string('payment_status')->nullable()->after('payment_method');
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->text('gateway_response')->nullable()->after('transaction_id');
            $table->timestamp('paid_at')->nullable()->after('gateway_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'transaction_id', 'gateway_response', 'paid_at']);
        });
    }
};
