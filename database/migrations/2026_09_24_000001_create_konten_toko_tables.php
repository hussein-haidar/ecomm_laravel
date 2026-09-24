<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Template FAQ global (dikelola superadmin sebagai default platform)
        Schema::create('template_faq', function (Blueprint $table) {
            $table->id('id_tpl_faq');
            $table->string('kategori', 50);
            $table->string('pertanyaan', 500);
            $table->text('jawaban');
            $table->integer('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('kategori');
        });

        // Template Syarat & Ketentuan global (superadmin)
        Schema::create('template_syaket', function (Blueprint $table) {
            $table->id('id_tpl_syaket');
            $table->enum('tipe', ['intro', 'pasal'])->default('pasal');
            $table->string('judul', 255)->nullable();
            $table->text('isi');
            $table->integer('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // FAQ per toko/lapak (dikelola pemilik/admin)
        Schema::create('faq_toko', function (Blueprint $table) {
            $table->id('id_faq_toko');
            $table->string('sesi_user', 100)->nullable();
            $table->string('kategori', 50);
            $table->string('pertanyaan', 500);
            $table->text('jawaban');
            $table->integer('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('sesi_user');
            $table->index('kategori');
        });

        // Syarat & Ketentuan per toko/lapak (dikelola pemilik/admin)
        Schema::create('syaket_toko', function (Blueprint $table) {
            $table->id('id_syaket_toko');
            $table->string('sesi_user', 100)->nullable();
            $table->enum('tipe', ['intro', 'pasal'])->default('pasal');
            $table->string('judul', 255)->nullable();
            $table->text('isi');
            $table->integer('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('sesi_user');
        });

        // Samakan collation dengan tabel lama (utf8mb4_general_ci)
        foreach (['template_faq', 'template_syaket', 'faq_toko', 'syaket_toko'] as $tabel) {
            DB::statement("ALTER TABLE `{$tabel}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('template_faq');
        Schema::dropIfExists('template_syaket');
        Schema::dropIfExists('faq_toko');
        Schema::dropIfExists('syaket_toko');
    }
};