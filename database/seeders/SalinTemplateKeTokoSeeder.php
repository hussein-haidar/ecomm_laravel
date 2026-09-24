<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalinTemplateKeTokoSeeder extends Seeder
{
    /**
     * Salin template FAQ & S&K superadmin ke tabel toko (faq_toko/syaket_toko)
     * untuk setiap toko (sesi_user) yang terdaftar di tabel website.
     */
    public function run(): void
    {
        // Ambil daftar sesi_user pemilik website (unik)
        $daftarToko = DB::table('website')
            ->select('sesi_user')
            ->distinct()
            ->whereNotNull('sesi_user')
            ->pluck('sesi_user');

        if ($daftarToko->isEmpty()) {
            $this->command->info('Tidak ada toko terdaftar di tabel website.');
            return;
        }

        foreach ($daftarToko as $sesiUser) {
            // FAQ toko
            DB::table('faq_toko')->where('sesi_user', $sesiUser)->delete();
            $rows = [];
            foreach (DB::table('template_faq')->orderBy('urutan')->orderBy('id_tpl_faq')->get() as $tpl) {
                $rows[] = [
                    'sesi_user' => $sesiUser,
                    'kategori' => $tpl->kategori,
                    'pertanyaan' => $tpl->pertanyaan,
                    'jawaban' => $tpl->jawaban,
                    'urutan' => $tpl->urutan,
                    'status' => $tpl->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($rows)) {
                DB::table('faq_toko')->insert($rows);
            }

            // S&K toko
            DB::table('syaket_toko')->where('sesi_user', $sesiUser)->delete();
            $rowsSyaket = [];
            foreach (DB::table('template_syaket')->orderBy('urutan')->orderBy('id_tpl_syaket')->get() as $tpl) {
                $rowsSyaket[] = [
                    'sesi_user' => $sesiUser,
                    'tipe' => $tpl->tipe,
                    'judul' => $tpl->judul,
                    'isi' => $tpl->isi,
                    'urutan' => $tpl->urutan,
                    'status' => $tpl->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($rowsSyaket)) {
                DB::table('syaket_toko')->insert($rowsSyaket);
            }
        }

        $this->command->info("Konten template disalin ke {$daftarToko->count()} toko.");
    }
}