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
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->foreignId('id_cst')->constrained('customers', 'id_cst')->cascadeOnDelete();
            $table->foreignId('id_barang')->constrained('barangs', 'id_barang')->cascadeOnDelete();
            $table->foreignId('id_ukuran')->nullable()->constrained('ukurans', 'id_ukuran')->nullOnDelete();
            $table->unsignedInteger('jumlah_barang')->default(1);
            $table->timestamps();

            $table->index(['id_cst', 'id_barang']);
            $table->unique(['id_cst', 'id_barang', 'id_ukuran'], 'keranjang_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};