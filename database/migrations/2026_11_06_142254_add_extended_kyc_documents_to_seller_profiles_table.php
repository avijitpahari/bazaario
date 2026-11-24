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
            if (!Schema::hasColumn('seller_profiles', 'aadhaar_number')) {
                $table->string('aadhaar_number', 50)->nullable()->after('fssai_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'aadhaar_doc_path')) {
                $table->string('aadhaar_doc_path', 255)->nullable()->after('aadhaar_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'mandi_license_number')) {
                $table->string('mandi_license_number', 50)->nullable()->after('aadhaar_doc_path');
            }
            if (!Schema::hasColumn('seller_profiles', 'mandi_license_doc_path')) {
                $table->string('mandi_license_doc_path', 255)->nullable()->after('mandi_license_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'udyam_number')) {
                $table->string('udyam_number', 50)->nullable()->after('mandi_license_doc_path');
            }
            if (!Schema::hasColumn('seller_profiles', 'udyam_doc_path')) {
                $table->string('udyam_doc_path', 255)->nullable()->after('udyam_number');
            }
            if (!Schema::hasColumn('seller_profiles', 'provided_documents')) {
                $table->json('provided_documents')->nullable()->after('document_statuses');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $cols = [
                'aadhaar_number',
                'aadhaar_doc_path',
                'mandi_license_number',
                'mandi_license_doc_path',
                'udyam_number',
                'udyam_doc_path',
                'provided_documents',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('seller_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
