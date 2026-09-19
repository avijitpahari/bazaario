<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 280)->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->enum('sale_type', ['fixed_price', 'auction'])->default('fixed_price');
            $table->decimal('price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('sku', 100)->nullable()->unique();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->unsignedInteger('processing_time_days')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'archived'])->default('draft');
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('seller_id', 'idx_products_seller');
            $table->index('category_id', 'idx_products_category');
            $table->index('sale_type', 'idx_products_sale_type');
            $table->index('status', 'idx_products_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
