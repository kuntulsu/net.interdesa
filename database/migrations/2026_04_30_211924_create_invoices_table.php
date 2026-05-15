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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pelanggan_id");
            $table->string("invoice_number")->unique();
            $table->string("name");
            $table->decimal("amount", 10, 2);
            $table->date("due_date");
            $table->integer("status")->default(0); // 0: unpaid, 1: paid, 2: overdue
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
