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
            $table->string('no_resi', 50)->nullable()->after('snap_token');
            $table->timestamp('delivered_at')->nullable()->after('no_resi');

            $table->enum('status', [
                'pending', 'paid', 'expired', 'cancelled', 'refunded', 'partially_refunded', 'deny',
                'processed', 'shipping', 'delivered', 'completed',
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'refunded', 'partially_refunded', 'deny'])
                ->default('pending')->change();
            $table->dropColumn(['no_resi', 'delivered_at']);
        });
    }
};
