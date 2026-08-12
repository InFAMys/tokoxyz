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
        DB::table('barangs')->whereNull('berat')->update(['berat' => 0]);

        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('berat', 8, 3)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('berat', 8, 3)->nullable()->change();
        });
    }
};
