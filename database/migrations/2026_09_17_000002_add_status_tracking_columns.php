<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // kurir.tipe_kurir (referensi updateStatusOtomatis & CRUD kurir)
        if (! Schema::hasColumn('kurir', 'tipe_kurir')) {
            Schema::table('kurir', function (Blueprint $table) {
                $table->string('tipe_kurir', 20)->nullable()->after('jenis_kurir');
            });
        }

        DB::statement("UPDATE `kurir` SET `tipe_kurir` = 'instan' WHERE `tipe_kurir` IS NULL");

        // ekspedisi.waktu_mulai_tahap & updated_at
        if (! Schema::hasColumn('ekspedisi', 'waktu_mulai_tahap')) {
            Schema::table('ekspedisi', function (Blueprint $table) {
                $table->dateTime('waktu_mulai_tahap')->nullable()->after('status_kirim');
            });
        }

        if (! Schema::hasColumn('ekspedisi', 'updated_at')) {
            Schema::table('ekspedisi', function (Blueprint $table) {
                $table->dateTime('updated_at')->nullable()->after('waktu_mulai_tahap');
            });
        }

        // lacak_pesanan: kolom status & waktu tambahan
        if (! Schema::hasColumn('lacak_pesanan', 'waktu_tiba_tujuan')) {
            Schema::table('lacak_pesanan', function (Blueprint $table) {
                $table->dateTime('waktu_tiba_tujuan')->nullable()->after('waktu_sampai_tujuan');
            });
        }

        DB::statement(
            "UPDATE `lacak_pesanan`
             SET `waktu_tiba_tujuan` = `waktu_sampai_tujuan`
             WHERE `waktu_tiba_tujuan` IS NULL
               AND `waktu_sampai_tujuan` IS NOT NULL
               AND `waktu_sampai_tujuan` <> '0000-00-00 00:00:00'
               AND YEAR(`waktu_sampai_tujuan`) > 0"
        );

        if (! Schema::hasColumn('lacak_pesanan', 'waktu_pesanan_diterima')) {
            Schema::table('lacak_pesanan', function (Blueprint $table) {
                $table->dateTime('waktu_pesanan_diterima')->nullable()->after('waktu_tiba_tujuan');
            });
        }

        if (! Schema::hasColumn('lacak_pesanan', 'status_keterlambatan')) {
            Schema::table('lacak_pesanan', function (Blueprint $table) {
                $table->string('status_keterlambatan', 20)->nullable()->after('waktu_pesanan_diterima');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'kurir'        => ['tipe_kurir'],
            'ekspedisi'    => ['waktu_mulai_tahap', 'updated_at'],
            'lacak_pesanan' => ['waktu_tiba_tujuan', 'waktu_pesanan_diterima', 'status_keterlambatan'],
        ];

        foreach ($columns as $table => $names) {
            Schema::table($table, function (Blueprint $blueprint) use ($names) {
                $blueprint->dropColumn($names);
            });
        }
    }
};