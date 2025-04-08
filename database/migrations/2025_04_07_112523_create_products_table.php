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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Pricing & Stock
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);

            // Product Type
            $table->enum('unit', ['kg', 'litre', 'bag', 'crate', 'bunch', 'piece', 'dozen', 'other'])->default('kg');
            $table->string('measurement')->nullable(); // e.g., "5kg", "1 dozen"

            // Ownership & Category
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Seller
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');

            // Media
            $table->string('thumbnail')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
