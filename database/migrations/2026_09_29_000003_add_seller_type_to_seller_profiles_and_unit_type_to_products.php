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
        if (!Schema::hasColumn('seller_profiles', 'seller_type')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                $table->string('seller_type')->default('Kirana Store')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('products', 'unit_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('unit_type')->default('piece')->nullable()->after('sale_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('seller_profiles', 'seller_type')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                $table->dropColumn('seller_type');
            });
        }

        if (Schema::hasColumn('products', 'unit_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit_type');
            });
        }
    }
};
