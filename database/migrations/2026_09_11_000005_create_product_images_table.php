<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes product_id but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('product_id');
            $table->string('image_path', 255);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('product_id', 'idx_product_images_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
