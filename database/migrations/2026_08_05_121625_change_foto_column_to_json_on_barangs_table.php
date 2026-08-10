<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('barangs')
            ->select(['id_barang', 'foto'])
            ->orderBy('id_barang')
            ->cursor()
            ->each(function (object $barang): void {
                $photos = json_decode($barang->foto, true);

                if (! is_array($photos)) {
                    $photos = $barang->foto === '' ? [] : [$barang->foto];
                }

                DB::table('barangs')
                    ->where('id_barang', $barang->id_barang)
                    ->update(['foto' => json_encode($photos)]);
            });

        Schema::table('barangs', function (Blueprint $table) {
            $table->json('foto')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->text('foto')->change();
        });

        DB::table('barangs')
            ->select(['id_barang', 'foto'])
            ->orderBy('id_barang')
            ->cursor()
            ->each(function (object $barang): void {
                $photos = json_decode($barang->foto, true);

                DB::table('barangs')
                    ->where('id_barang', $barang->id_barang)
                    ->update(['foto' => is_array($photos) ? ($photos[0] ?? '') : $barang->foto]);
            });

        Schema::table('barangs', function (Blueprint $table) {
            $table->string('foto')->change();
        });
    }
};
