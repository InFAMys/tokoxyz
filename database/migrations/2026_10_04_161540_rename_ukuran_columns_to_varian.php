<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign('keranjang_id_ukuran_foreign');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign('checkout_items_id_ukuran_foreign');
        });

        Schema::table('varians', function (Blueprint $table) {
            $table->renameColumn('id_ukuran', 'id_varian');
            $table->renameColumn('nama_ukuran', 'nama_varian');
            $table->renameColumn('ukuran', 'varian');
            $table->renameColumn('harga_ukuran', 'harga_varian');
            $table->renameColumn('stok_ukuran', 'stok_varian');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->renameColumn('id_ukuran', 'id_varian');
            $table->renameColumn('ukuran_name', 'varian_name');
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->renameColumn('id_ukuran', 'id_varian');
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->foreign('id_varian')->references('id_varian')->on('varians')->nullOnDelete();
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->foreign('id_varian')->references('id_varian')->on('varians')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropForeign('keranjang_id_varian_foreign');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropForeign('detail_pesanan_id_varian_foreign');
        });

        Schema::table('varians', function (Blueprint $table) {
            $table->renameColumn('id_varian', 'id_ukuran');
            $table->renameColumn('nama_varian', 'nama_ukuran');
            $table->renameColumn('varian', 'ukuran');
            $table->renameColumn('harga_varian', 'harga_ukuran');
            $table->renameColumn('stok_varian', 'stok_ukuran');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->renameColumn('id_varian', 'id_ukuran');
            $table->renameColumn('varian_name', 'ukuran_name');
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->renameColumn('id_varian', 'id_ukuran');
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->foreign('id_ukuran')->references('id_ukuran')->on('varians')->nullOnDelete();
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->foreign('id_ukuran')->references('id_ukuran')->on('varians')->nullOnDelete();
        });
    }
};
