<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'kode_kupon')) {
                $table->string('kode_kupon')->nullable()->after('ongkir');
            }
            if (!Schema::hasColumn('pembayaran', 'diskon')) {
                $table->decimal('diskon', 12, 2)->default(0)->after('kode_kupon');
            }
        });
    }

    public function down()
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran', 'kode_kupon')) {
                $table->dropColumn('kode_kupon');
            }
            if (Schema::hasColumn('pembayaran', 'diskon')) {
                $table->dropColumn('diskon');
            }
        });
    }
};
