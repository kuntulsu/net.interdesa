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
        Schema::create("pelanggan", function (Blueprint $table) {
            $table->id();
            $table->decimal("nik", 16, 0);
            $table->string("nama");
            $table->string("alamat")->nullable();
            $table->double("telp");
            $table->date("jatuh_tempo");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("pelanggan");
    }
};
