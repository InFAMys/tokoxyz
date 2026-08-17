<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE diskons MODIFY COLUMN status_diskon ENUM('aktif','nonaktif','kadaluarsa') NOT NULL DEFAULT 'nonaktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE diskons MODIFY COLUMN status_diskon ENUM('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif'");
    }
};
