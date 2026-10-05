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
        // 1. Extend products table with freshness and perishable tracking
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'harvest_date')) {
                $table->date('harvest_date')->nullable()->after('unit_type');
            }
            if (!Schema::hasColumn('products', 'expiry_days')) {
                $table->unsignedSmallInteger('expiry_days')->nullable()->after('harvest_date');
            }
            if (!Schema::hasColumn('products', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('expiry_days');
            }
            if (!Schema::hasColumn('products', 'is_perishable')) {
                $table->boolean('is_perishable')->default(false)->after('expiry_date');
            }
            if (!Schema::hasColumn('products', 'auto_hide_expired')) {
                $table->boolean('auto_hide_expired')->default(true)->after('is_perishable');
            }
            if (!Schema::hasColumn('products', 'farm_origin')) {
                $table->string('farm_origin', 255)->nullable()->after('auto_hide_expired');
            }
            if (!Schema::hasColumn('products', 'harvest_grade')) {
                $table->string('harvest_grade', 50)->nullable()->after('farm_origin');
            }
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedSmallInteger('low_stock_threshold')->default(10)->after('stock');
            }
        });

        // 2. Extend seller_orders table with delivery slot
        Schema::table('seller_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_orders', 'delivery_slot')) {
                $table->string('delivery_slot', 100)->nullable()->after('status');
            }
        });

        // 3. Extend seller_profiles table with address, postal_code, and operating radius
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'address')) {
                $table->string('address', 255)->nullable()->after('country');
            }
            if (!Schema::hasColumn('seller_profiles', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('address');
            }
            if (!Schema::hasColumn('seller_profiles', 'operating_radius_km')) {
                $table->unsignedSmallInteger('operating_radius_km')->default(25)->nullable()->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert seller_profiles additions
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'operating_radius_km')) {
                $table->dropColumn('operating_radius_km');
            }
            if (Schema::hasColumn('seller_profiles', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('seller_profiles', 'address')) {
                $table->dropColumn('address');
            }
        });

        // 2. Revert seller_orders additions
        Schema::table('seller_orders', function (Blueprint $table) {
            if (Schema::hasColumn('seller_orders', 'delivery_slot')) {
                $table->dropColumn('delivery_slot');
            }
        });

        // 3. Revert products additions
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'harvest_date',
                'expiry_days',
                'expiry_date',
                'is_perishable',
                'auto_hide_expired',
                'farm_origin',
                'harvest_grade',
                'low_stock_threshold',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
