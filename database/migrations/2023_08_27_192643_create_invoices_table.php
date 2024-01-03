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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('series')->default('WBX');
            $table->date('start_at')->nullable();
            $table->date('due_at')->nullable();
            $table->unsignedBigInteger('clients_id'); // Adaugă această linie pentru a crea coloana clientis_id
            $table->string('total')->nullable();
            $table->unsignedBigInteger('products_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
