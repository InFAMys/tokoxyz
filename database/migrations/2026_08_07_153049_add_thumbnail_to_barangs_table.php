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
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('foto');
        });

        DB::table('barangs')
            ->select(['id_barang', 'foto'])
            ->orderBy('id_barang')
            ->cursor()
            ->each(function (object $barang): void {
                $photos = json_decode($barang->foto, true);

                DB::table('barangs')
                    ->where('id_barang', $barang->id_barang)
                    ->update(['thumbnail' => is_array($photos) ? ($photos[0] ?? null) : null]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
