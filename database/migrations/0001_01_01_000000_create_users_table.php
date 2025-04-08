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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            // Password + security
            $table->string('password');
            $table->string('ver_code')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->rememberToken();

            // Roles & Permissions
            $table->boolean('is_seller')->default(false);
            $table->boolean('is_buyer')->default(true);
            $table->boolean('is_admin')->default(false);

            // Phone Numbers
            $table->string('phone')->nullable();              // primary contact
            $table->string('alt_phone')->nullable();          // optional backup

            // Location & Address Info
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Nigeria');    // default to NG, change if needed

            // Profile Details (for sellers especially)
            $table->text('bio')->nullable();                  // seller about section
            $table->string('profile_photo')->nullable();      // profile picture URL
            $table->string('farm_name')->nullable();          // optional seller alias/farm name

            $table->decimal('delivery_fee', 10, 2)->nullable();

            // For ratings
            $table->float('avg_delivery_rating')->default(0);
            $table->float('avg_quality_rating')->default(0);
            $table->integer('total_reviews')->default(0);

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('reset_code_expires_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
