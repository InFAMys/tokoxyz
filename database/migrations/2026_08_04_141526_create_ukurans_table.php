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
        Schema::create('ukurans', function (Blueprint $table) {
            $table->id('id_ukuran');
            $table->foreignId('id_barang')->constrained('barangs', 'id_barang')->cascadeOnDelete();
            $table->string('nama_ukuran', 10);
            $table->string('ukuran')->nullable();
            $table->integer('stok_ukuran');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ukurans');
    }
};