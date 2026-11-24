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
        Schema::create('admin_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->default('system')->index(); // order, seller, auction, payout, kyc, dispute, system
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('notifications');
            $table->string('badge_text', 50)->default('Update');
            $table->string('badge_color', 30)->default('blue'); // emerald, amber, blue, purple, rose, slate
            $table->string('action_url')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_feeds');
    }
};
