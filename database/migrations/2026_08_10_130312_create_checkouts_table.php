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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_checkout');
            $table->foreignId('id_cst')->constrained('customers', 'id_cst')->cascadeOnDelete();
            $table->foreignId('id_alamat')->constrained('alamat', 'id_alamat');
            $table->string('order_id', 50)->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_telp')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('berat_total', 8, 3)->default(0);
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('kode_diskon', 10)->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'refunded', 'partially_refunded', 'deny'])
                ->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
