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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['waiting', 'process', 'completed'])->default('waiting');
            $table->text("images")->nullable(); // JSON encoded array of image URLs
            $table->foreignId('user_id')->constrained(); // The ticket creator
            $table->foreignId('solver')->nullable()->constrained('users'); // Optional, the staff
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
