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
        Schema::create("tagihan", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("pelanggan_id")->nullable();
            $table->string("tipe_tagihan")->default("LAINYA");
            $table->decimal("nominal_tagihan", 10, 0)->nullable();
            $table->dateTime("end_date")->nullable();
            $table->timestamps();

            $table->foreign("pelanggan_id")
                ->references("id")
                ->on("pelanggan");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tagihan");
    }
};
