<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_id')
                ->constrained('orders')->cascadeOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->timestamp('used_at')->nullable()->useCurrent();

            $table->unique(['coupon_id', 'order_id'], 'uq_coupon_usage_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
