<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['pembayaran', 'pembelian', 'keranjang', 'ekspedisi'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'id_pelanggan')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->integer('id_pelanggan')->nullable()->after('nama_pelanggan');
                $blueprint->index('id_pelanggan', "idx_{$table}_id_pelanggan");
            });
        }

        foreach (['pembayaran', 'pembelian', 'keranjang', 'ekspedisi'] as $table) {
            DB::statement(
                "UPDATE `{$table}` p
                 LEFT JOIN `pelanggan` c ON c.`nama_pelanggan` = p.`nama_pelanggan`
                 SET p.`id_pelanggan` = c.`id_pelanggan`"
            );
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'id_pelanggan')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropIndex("idx_{$table}_id_pelanggan");
                $blueprint->dropColumn('id_pelanggan');
            });
        }
    }
};