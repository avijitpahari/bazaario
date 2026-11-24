<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (!Schema::hasColumn('wishlists', 'sharing_token')) {
                $table->string('sharing_token', 64)->nullable()->unique()->after('user_id');
            }
            if (!Schema::hasColumn('wishlists', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('sharing_token');
            }
            if (!Schema::hasColumn('wishlists', 'title')) {
                $table->string('title', 120)->nullable()->after('is_public');
            }
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            if (!Schema::hasColumn('wishlist_items', 'note')) {
                $table->string('note', 255)->nullable()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn(['sharing_token', 'is_public', 'title']);
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropColumn(['note']);
        });
    }
};
