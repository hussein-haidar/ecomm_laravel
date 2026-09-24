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
        Schema::table('website', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu')->after('status_website');
            $table->text('alasan_ditolak')->nullable()->after('status_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'alasan_ditolak']);
        });
    }
};
