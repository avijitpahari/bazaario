<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')
                ->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')->cascadeOnDelete();
            $table->string('reason', 255);
            $table->text('description')->nullable();
            $table->enum('status', [
                'requested',
                'approved',
                'rejected',
                'pickup_scheduled',
                'received',
                'refund_processing',
                'refunded',
            ])->default('requested');
            $table->decimal('refund_amount', 12, 2)->default(0.00);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
