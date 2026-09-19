<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title', 255)->nullable();
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'product_id'], 'uq_user_product_review');
            $table->index('product_id', 'fk_reviews_product');
            $table->index('order_item_id', 'fk_reviews_order_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
