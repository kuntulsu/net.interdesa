<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\TipeTransaksi;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("transaksi", function (Blueprint $table) {
            $table->id();
            $table->enum("tipe", [
                TipeTransaksi::KELUAR->name,
                TipeTransaksi::MASUK->name,
            ]);
            $table->decimal("jumlah", 10, 0);
            $table->string("keterangan");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("transaksi");
    }
};
