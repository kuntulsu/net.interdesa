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
        Schema::create("bukti_transaksi", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("transaksi_id");
            $table->string("bukti");
            $table->string("keterangan")->nullable();
            $table->timestamps();

            $table
                ->foreign("transaksi_id")
                ->references("id")
                ->on("transaksi")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("bukti_transaksi");
    }
};
