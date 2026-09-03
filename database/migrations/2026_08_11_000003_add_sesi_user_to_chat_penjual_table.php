<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_penjual', function (Blueprint $table) {
            $table->string('sesi_user', 100)->nullable()->after('id_user');
            $table->index('sesi_user');
        });

        // Backfill sesi_user dari website berdasarkan id_user (pemilik toko)
        DB::statement('UPDATE chat_penjual cp JOIN website w ON w.id_user = cp.id_user SET cp.sesi_user = w.sesi_user WHERE cp.sesi_user IS NULL');
    }

    public function down()
    {
        Schema::table('chat_penjual', function (Blueprint $table) {
            $table->dropIndex(['sesi_user']);
            $table->dropColumn('sesi_user');
        });
    }
};
