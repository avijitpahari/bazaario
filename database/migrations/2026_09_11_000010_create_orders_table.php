<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->enum('order_type', ['cart', 'auction'])->default('cart');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['cod', 'card', 'upi', 'net_banking', 'wallet'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->string('delivery_full_name', 150);
            $table->string('delivery_phone', 30);
            $table->string('delivery_address_line_1', 255);
            $table->string('delivery_address_line_2', 255)->nullable();
            $table->string('delivery_city', 100);
            $table->string('delivery_state', 100);
            $table->string('delivery_country', 100);
            $table->string('delivery_postal_code', 20);
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('coupon_id', 'fk_orders_coupon');
            $table->index('user_id', 'idx_orders_user');
            $table->index('order_status', 'idx_orders_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
