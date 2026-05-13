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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requested_book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('offered_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
