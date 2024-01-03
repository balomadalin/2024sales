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
            $table->string('name_product');
            $table->text('description_product')->nullable();
            $table->string('unit'); // modificat pentru a evita spațiile
            $table->decimal('tva', 4, 2)->nullable();
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 8, 2);
            $table->string('product_value');
            $table->string('discount', 8, 2)->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('estimate_id')->nullable();
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
