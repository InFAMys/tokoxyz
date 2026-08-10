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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('id_barang');
            $table->foreignId('id_brand')->constrained('brands', 'id_brand')->cascadeOnDelete();
            $table->foreignId('id_kategori')->constrained('kategoris', 'id_kategori')->cascadeOnDelete();
            $table->string('kode_barang', 15);
            $table->string('nama_barang', 32);
            $table->text('deskripsi');
            $table->string('foto');
            $table->decimal('harga', 12, 2);
            $table->integer('stok');
            $table->enum('status',['Ditampilkan','Disembunyikan'])->default('Disembunyikan');
            $table->enum('preorder',['Tersedia','Tidak Tersedia'])->default('Tidak Tersedia');
            $table->integer('estimasi_preorder')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
