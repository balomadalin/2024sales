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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cui');
            $table->string('rc');
            $table->string('bank')->nullable();
            $table->string('iban')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('state');
            $table->string('city');
            $table->text('address')->nullable();
            $table->string('person')->nullable();
            $table->string('position')->nullable();
            $table->text('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
