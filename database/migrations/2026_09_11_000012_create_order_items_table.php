<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('seller_order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name', 255);
            $table->string('product_image', 255)->nullable();
            $table->string('sku', 100)->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('seller_order_id', 'fk_order_items_seller_order');
            $table->index('product_id', 'fk_order_items_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
