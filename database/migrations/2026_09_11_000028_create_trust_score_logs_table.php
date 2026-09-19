<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_score_logs', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes seller_id but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('seller_id');
            $table->decimal('fulfillment_rate', 5, 2)->default(0.00);
            $table->decimal('average_rating', 5, 2)->default(0.00);
            $table->decimal('verification_bonus', 5, 2)->default(0.00);
            $table->decimal('computed_score', 5, 2)->default(0.00);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('seller_id', 'fk_trust_score_seller');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_score_logs');
    }
};
