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
        Schema::create("pembayaran_pelanggan", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pelanggan_id");
            $table->unsignedBigInteger("tagihan_id");
            $table->unsignedBigInteger("user_id");
            $table->decimal("nominal_tagihan", 10, 0);

            $table->unique(["pelanggan_id", "tagihan_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("pembayaran_pelanggan");
    }
};
