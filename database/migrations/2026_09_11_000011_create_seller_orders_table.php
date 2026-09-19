<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_orders', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('seller_order_number', 60)->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_amount', 12, 2)->default(0.00);
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->decimal('commission_amount', 12, 2)->default(0.00);
            $table->decimal('payout_amount', 12, 2)->default(0.00);
            $table->enum('status', ['placed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned'])->default('placed');
            $table->string('tracking_number', 150)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('order_id', 'idx_seller_orders_order');
            $table->index('seller_id', 'idx_seller_orders_seller');
            $table->index('status', 'idx_seller_orders_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_orders');
    }
};
