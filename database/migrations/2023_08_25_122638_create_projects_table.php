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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('clients_id'); // Adaugă această linie pentru a crea coloana clientis_id
            $table->date('start_at')->nullable();
            $table->date('due_at')->nullable();
            $table->string('pricing_unit')->nullable();
            $table->string('total')->nullable();

            $table->boolean('aborted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
