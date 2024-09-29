<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("profil_pelanggan", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pelanggan_id")->unique();
            $table->string("secret_id")->unique();

            $table
                ->foreign("pelanggan_id")
                ->references("id")
                ->on("pelanggan")
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("profil_pelanggan");
    }
};
