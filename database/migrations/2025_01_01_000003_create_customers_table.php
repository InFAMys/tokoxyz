<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('id_cst');
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->char('no_telp', length: 12)->unique();
            $table->string('password');
            $table->enum('member', ['true', 'false'])->default('false');
            $table->timestamp('member_since')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};