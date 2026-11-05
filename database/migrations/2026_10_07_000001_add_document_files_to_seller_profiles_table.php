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
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'gstin_doc_path')) {
                $table->string('gstin_doc_path', 255)->nullable()->after('fssai_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'trade_license_doc_path')) {
                $table->string('trade_license_doc_path', 255)->nullable()->after('gstin_doc_path');
            }
            if (!Schema::hasColumn('seller_profiles', 'bank_doc_path')) {
                $table->string('bank_doc_path', 255)->nullable()->after('trade_license_doc_path');
            }
            if (!Schema::hasColumn('seller_profiles', 'fssai_doc_path')) {
                $table->string('fssai_doc_path', 255)->nullable()->after('bank_doc_path');
            }
            if (!Schema::hasColumn('seller_profiles', 'document_statuses')) {
                $table->json('document_statuses')->nullable()->after('fssai_doc_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $cols = ['gstin_doc_path', 'trade_license_doc_path', 'bank_doc_path', 'fssai_doc_path', 'document_statuses'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('seller_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
