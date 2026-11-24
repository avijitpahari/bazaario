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
        Schema::create('auction_disputes', function (Blueprint $table) {
            $table->id();
            $table->string('dispute_number', 32)->unique()->index();
            $table->foreignId('auction_id')->constrained('auctions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->nullable()->constrained('seller_profiles')->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('dispute_type', 50)->default('quality_mismatch')->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->string('status', 30)->default('open')->index();
            $table->string('subject', 255);
            $table->text('claim_description');
            $table->decimal('contested_amount', 12, 2)->default(0);
            $table->json('evidence_urls')->nullable();
            $table->string('ruling', 50)->nullable()->index();
            $table->text('ruling_notes')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->foreignId('arbitrated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('arbitrated_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('support_tickets') && !Schema::hasColumn('support_tickets', 'auction_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->foreignId('auction_id')->nullable()->after('order_item_id')->constrained('auctions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('support_tickets') && Schema::hasColumn('support_tickets', 'auction_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropForeign(['auction_id']);
                $table->dropColumn('auction_id');
            });
        }

        Schema::dropIfExists('auction_disputes');
    }
};
