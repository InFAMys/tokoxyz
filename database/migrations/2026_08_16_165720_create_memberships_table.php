<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id('id_membership');
            $table->unsignedBigInteger('id_cst');
            $table->string('order_id', 50)->unique();
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['pending', 'paid', 'expired', 'deny', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('id_cst')->references('id_cst')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
