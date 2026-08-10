<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id('id_pegawai');
            $table->string('nama_pegawai');
            $table->string('username_pegawai');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};