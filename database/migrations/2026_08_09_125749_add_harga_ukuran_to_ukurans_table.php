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
        Schema::table('ukurans', function (Blueprint $table) {
            $table->decimal('harga_ukuran', 10, 2)->after('ukuran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ukurans', function (Blueprint $table) {
            $table->dropColumn('harga_ukuran');
        });
    }
};
