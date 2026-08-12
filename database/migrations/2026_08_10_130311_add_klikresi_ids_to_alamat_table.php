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
        Schema::table('alamat', function (Blueprint $table) {
            $table->string('id_provinsi')->after('provinsi')->nullable();
            $table->string('id_kota')->after('id_provinsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alamat', function (Blueprint $table) {
            $table->dropColumn(['id_provinsi', 'id_kota']);
        });
    }
};
