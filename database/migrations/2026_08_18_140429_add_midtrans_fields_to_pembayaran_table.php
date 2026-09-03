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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('id_bayar');
            $table->text('midtrans_qr_string')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_qr_url')->nullable()->after('midtrans_qr_string');
            $table->string('midtrans_status')->nullable()->after('midtrans_qr_url');
            $table->text('midtrans_response')->nullable()->after('midtrans_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_qr_string',
                'midtrans_qr_url',
                'midtrans_status',
                'midtrans_response',
            ]);
        });
    }
};
