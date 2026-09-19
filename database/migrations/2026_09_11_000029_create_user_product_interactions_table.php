<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_product_interactions', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->enum('interaction_type', ['view', 'search_click', 'cart', 'wishlist', 'purchase', 'compare']);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['user_id', 'interaction_type'], 'idx_interactions_user_type');
            $table->index(['product_id', 'interaction_type'], 'idx_interactions_product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_product_interactions');
    }
};
