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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Grāmatas nosaukums
            $table->string('author')->nullable();   // Autors
            $table->year('publication_date')->nullable();   // Grāmatas izdošanas datums
            $table->string('state')->nullable();    // Grāmatas stāvoklis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
