<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')->cascadeOnDelete();
            // NOTE: seller_id references seller_profiles.id (not users.id) in this schema
            $table->foreignId('seller_id')
                ->constrained('seller_profiles')->cascadeOnDelete();
            $table->decimal('starting_price', 12, 2);
            $table->decimal('reserve_price', 12, 2)->nullable();
            $table->decimal('current_price', 12, 2);
            $table->decimal('minimum_increment', 12, 2)->default(100.00);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['scheduled', 'live', 'ended', 'cancelled'])->default('scheduled');
            $table->foreignId('winner_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status', 'idx_auctions_status');
            $table->index(['starts_at', 'ends_at'], 'idx_auctions_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
