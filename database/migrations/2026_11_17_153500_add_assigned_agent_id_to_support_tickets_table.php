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
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'assigned_agent_id')) {
                $table->foreignId('assigned_agent_id')
                    ->nullable()
                    ->after('seller_id')
                    ->constrained('users')
                    ->onDelete('set null');
            }
        });

        Schema::table('support_ticket_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('support_ticket_messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('attachment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_ticket_messages', function (Blueprint $table) {
            if (Schema::hasColumn('support_ticket_messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'assigned_agent_id')) {
                $table->dropForeign(['assigned_agent_id']);
                $table->dropColumn('assigned_agent_id');
            }
        });
    }
};
