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
        Schema::create('diskons', function (Blueprint $table) {
            $table->id('id_diskon');
            $table->string('nama_diskon', 30);
            $table->decimal('jumlah_diskon', 12, 2);
            $table->string('kode_diskon', 10)->unique();
            $table->dateTime('mulai_diskon');
            $table->dateTime('akhir_diskon');
            $table->enum('status_diskon', ['aktif','nonaktif'])->default('nonaktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskons');
    }
};
