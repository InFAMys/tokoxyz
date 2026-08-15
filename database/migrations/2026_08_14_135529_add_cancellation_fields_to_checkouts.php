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
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('cancel_reason', 255)->nullable()->after('payment_type');
            $table->string('cancel_from', 20)->nullable()->after('cancel_reason');
            $table->string('cancel_response', 255)->nullable()->after('cancel_from');
            $table->timestamp('cancel_requested_at')->nullable()->after('cancel_response');
        });

        DB::statement("ALTER TABLE checkouts MODIFY COLUMN status ENUM(
            'pending','paid','expired','cancelled','refunded','partially_refunded','deny',
            'processed','shipping','delivered','completed','cancel_pending'
        ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE checkouts MODIFY COLUMN status ENUM(
            'pending','paid','expired','cancelled','refunded','partially_refunded','deny',
            'processed','shipping','delivered','completed'
        ) NOT NULL DEFAULT 'pending'");

        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancel_from', 'cancel_response', 'cancel_requested_at']);
        });
    }
};
