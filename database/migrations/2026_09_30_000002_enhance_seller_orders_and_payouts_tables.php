<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seller_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_orders', 'courier_name')) {
                $table->string('courier_name', 150)->nullable()->after('delivery_slot');
            }
            if (!Schema::hasColumn('seller_orders', 'handover_confirmed_at')) {
                $table->timestamp('handover_confirmed_at')->nullable()->after('delivered_at');
            }
        });

        // Ensure status column accepts custom statuses like 'ready_for_pickup' and 'fulfilled'
        // In MySQL / SQLite, change column to string(50) if possible
        try {
            Schema::table('seller_orders', function (Blueprint $table) {
                $table->string('status', 50)->default('placed')->change();
            });
        } catch (\Throwable $e) {
            // Ignore if driver does not support alter column enum to string
        }

        Schema::table('payouts', function (Blueprint $table) {
            if (!Schema::hasColumn('payouts', 'apmc_cess')) {
                $table->decimal('apmc_cess', 12, 2)->default(0.00)->after('commission_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            if (Schema::hasColumn('payouts', 'apmc_cess')) {
                $table->dropColumn('apmc_cess');
            }
        });

        Schema::table('seller_orders', function (Blueprint $table) {
            if (Schema::hasColumn('seller_orders', 'handover_confirmed_at')) {
                $table->dropColumn('handover_confirmed_at');
            }
            if (Schema::hasColumn('seller_orders', 'courier_name')) {
                $table->dropColumn('courier_name');
            }
        });
    }
};
