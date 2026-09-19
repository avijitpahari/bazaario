<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->string('otp', 6);
            $table->timestamp('expires_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->nullable();

            $table->index('email', 'email_otps_email_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_otps');
    }
};
