<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes these columns but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('seller_order_id')->nullable();
            $table->decimal('gross_amount', 12, 2)->default(0.00);
            $table->decimal('commission_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'processing', 'paid', 'failed'])->default('pending');
            $table->string('payout_reference', 150)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('seller_id', 'fk_payouts_seller');
            $table->index('seller_order_id', 'fk_payouts_seller_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
