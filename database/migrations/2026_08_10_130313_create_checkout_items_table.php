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
        Schema::create('checkout_items', function (Blueprint $table) {
            $table->id('id_checkout_item');
            $table->foreignId('id_checkout')->constrained('checkouts', 'id_checkout')->cascadeOnDelete();
            $table->foreignId('id_barang')->nullable()->constrained('barangs', 'id_barang')->nullOnDelete();
            $table->foreignId('id_ukuran')->nullable()->constrained('ukurans', 'id_ukuran')->nullOnDelete();
            $table->string('nama_barang');
            $table->string('ukuran_name')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('jumlah_barang');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('berat', 8, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_items');
    }
};
