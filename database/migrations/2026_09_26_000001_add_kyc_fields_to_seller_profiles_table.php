<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('gstin', 20)->nullable()->after('trust_score');
            $table->string('pan_number', 20)->nullable()->after('gstin');
            $table->string('trade_license_number', 50)->nullable()->after('pan_number');
            $table->string('bank_account_number', 50)->nullable()->after('trade_license_number');
            $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number');
            $table->string('fssai_number', 30)->nullable()->after('bank_ifsc');
            $table->text('rejection_reason')->nullable()->after('fssai_number');
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'gstin',
                'pan_number',
                'trade_license_number',
                'bank_account_number',
                'bank_ifsc',
                'fssai_number',
                'rejection_reason',
            ]);
        });
    }
};
