<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('email_user', 100)->nullable()->unique()->after('fullname');
        });
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropUnique(['email_user']);
            $table->dropColumn('email_user');
        });
    }
};