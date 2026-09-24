<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailTokoToWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('website', function (Blueprint $table) {
            if (!Schema::hasColumn('website', 'email_toko')) {
                $table->string('email_toko', 191)->nullable()->after('wa_cabang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website', function (Blueprint $table) {
            if (Schema::hasColumn('website', 'email_toko')) {
                $table->dropColumn('email_toko');
            }
        });
    }
}