<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand role column so it accommodates 'support_agent', 'support', etc.
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user'");
            } catch (\Throwable $e) {}
        }

        // 2. Add support staff specific metadata
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'staff_id')) {
                $table->string('staff_id', 50)->nullable()->unique()->after('role');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable()->after('staff_id');
            }
            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('department');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'staff_id')) {
                $columnsToDrop[] = 'staff_id';
            }
            if (Schema::hasColumn('users', 'department')) {
                $columnsToDrop[] = 'department';
            }
            if (Schema::hasColumn('users', 'is_online')) {
                $columnsToDrop[] = 'is_online';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
