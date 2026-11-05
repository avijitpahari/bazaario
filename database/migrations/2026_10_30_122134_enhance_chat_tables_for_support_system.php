<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify existing columns in chat_conversations to allow guests & broader statuses
        try {
            DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        } catch (\Throwable $e) {
            // Already nullable or driver handled
        }

        try {
            DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN status VARCHAR(50) DEFAULT 'open'");
        } catch (\Throwable $e) {
            // Driver handled
        }

        Schema::table('chat_conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_conversations', 'visitor_id')) {
                $table->string('visitor_id', 64)->nullable()->index()->after('user_id');
            }
            if (!Schema::hasColumn('chat_conversations', 'guest_name')) {
                $table->string('guest_name', 150)->nullable()->after('visitor_id');
            }
            if (!Schema::hasColumn('chat_conversations', 'guest_email')) {
                $table->string('guest_email', 150)->nullable()->after('guest_name');
            }
            if (!Schema::hasColumn('chat_conversations', 'guest_phone')) {
                $table->string('guest_phone', 50)->nullable()->after('guest_email');
            }
            if (!Schema::hasColumn('chat_conversations', 'assigned_agent_id')) {
                $table->unsignedBigInteger('assigned_agent_id')->nullable()->index()->after('status');
            }
            if (!Schema::hasColumn('chat_conversations', 'department')) {
                $table->string('department', 50)->default('general')->after('assigned_agent_id');
            }
            if (!Schema::hasColumn('chat_conversations', 'priority')) {
                $table->string('priority', 20)->default('medium')->after('department');
            }
            if (!Schema::hasColumn('chat_conversations', 'tags')) {
                $table->json('tags')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('chat_conversations', 'channel')) {
                $table->string('channel', 50)->default('website')->after('tags');
            }
            if (!Schema::hasColumn('chat_conversations', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('channel');
            }
        });

        // 2. Enhance chat_messages for attachments & agent sender roles
        try {
            DB::statement("ALTER TABLE chat_messages MODIFY COLUMN role VARCHAR(50) DEFAULT 'user'");
        } catch (\Throwable $e) {
            // Driver handled
        }

        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'sender_type')) {
                $table->string('sender_type', 30)->default('user')->after('role'); // user, agent, bot, system
            }
            if (!Schema::hasColumn('chat_messages', 'sender_name')) {
                $table->string('sender_name', 150)->nullable()->after('sender_type');
            }
            if (!Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->string('attachment_path', 255)->nullable()->after('message');
            }
            if (!Schema::hasColumn('chat_messages', 'attachment_type')) {
                $table->string('attachment_type', 50)->nullable()->after('attachment_path'); // image, file
            }
            if (!Schema::hasColumn('chat_messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('attachment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['sender_type', 'sender_name', 'attachment_path', 'attachment_type', 'is_read']);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn([
                'visitor_id',
                'guest_name',
                'guest_email',
                'guest_phone',
                'assigned_agent_id',
                'department',
                'priority',
                'tags',
                'channel',
                'last_message_at',
            ]);
        });
    }
};
