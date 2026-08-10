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
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->foreignId('id_cst')->constrained('customers', 'id_cst')->cascadeOnDelete();
            $table->string('nama_alamat');
            $table->string('nama_penerima');
            $table->string('telp_penerima');
            $table->string('detail_alamat');
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('kode_pos', 10);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};