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
        Schema::create('offline_sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_type')->default('fixed');
            $table->decimal('total_amount', 10, 2);
            $table->string('order_type');
            $table->string('payment_method');
            $table->string('table_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->integer('guest_count')->default(1);
            $table->text('notes')->nullable();
            $table->string('reference_no')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('items')->nullable(); // Store order items as JSON
            $table->string('sync_token')->unique()->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('synced_at')->nullable();
            $table->text('sync_error')->nullable();
            $table->integer('sync_attempts')->default(0);

            $table->index(['status', 'created_at']);
            $table->index('sync_token');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_sync_queue');
    }
};
