<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // NOTE: dump indexes conversation_id but has no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('conversation_id');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('message');
            $table->string('tool_name', 150)->nullable();
            $table->json('tool_data')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['conversation_id', 'created_at'], 'idx_chat_messages_conversation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
