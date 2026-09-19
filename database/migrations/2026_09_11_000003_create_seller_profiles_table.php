<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            // NOTE: dump has a unique KEY on user_id but no ADD CONSTRAINT (no FK enforced)
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('shop_name', 150);
            $table->string('shop_slug', 180)->unique();
            $table->text('bio')->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->string('banner_path', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('trust_score', 5, 2)->default(0.00);
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index('status', 'idx_seller_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
