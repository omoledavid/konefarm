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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('email_from')->nullable();
            $table->longText('email_template')->nullable();
            $table->string('mail_config')->nullable();
            $table->string('global_shortcodes')->nullable();
            $table->tinyInteger('ev')->default(0);
            $table->boolean('auto_approve')->default(false)->comment('auto approval for products,');
            $table->boolean('register_status')->default(true);
            $table->boolean('deposit_status')->default(false);
            $table->boolean('withdraw_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
