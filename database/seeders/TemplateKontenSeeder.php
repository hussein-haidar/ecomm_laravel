<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateKontenSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFaq();
        $this->seedSyaket();
    }

    protected function seedFaq(): void
    {
        // Kosongkan template_faq dulu agar idempoten
        DB::table('template_faq')->truncate();

        $faqs = [
            'pemesanan' => [
                [
                    'q' => 'Bagaimana cara melakukan pemesanan?',
                    'a' => '1) Pilih produk yang diinginkan di halaman Katalog atau Toko.<br>
                           2) Pilih ukuran (jika ada) dan jumlah, lalu klik "Tambah ke Keranjang".<br>
                           3) Buka Keranjang (ikon keranjang di navbar), centang produk, klik "Checkout".<br>
                           4) Isi alamat pengiriman, pilih ekspedisi & metode pembayaran.<br>
                           5) Klik "Buat Pesanan" lalu lakukan pembayaran sesuai instruksi.',
                ],
                [
                    'q' => 'Apakah saya harus login untuk memesan?',
                    'a' => 'Ya. Anda harus mendaftar & login untuk checkout, melihat riwayat, melacak pengiriman, dan mengelola profil. Login juga memungkinkan fitur Chat Penjual & Wishlist.',
                ],
                [
                    'q' => 'Bisa ganti alamat/ukuran setelah pesanan dibuat?',
                    'a' => 'Tidak bisa diubah lewat sistem setelah checkout. Segera hubungi toko via <strong>Chat Penjual</strong> (di halaman Detail Produk / Keranjang / Riwayat) sebelum status berubah jadi "Dikemas/Dikirim".',
                ],
                [
                    'q' => 'Produk yang saya inginkan stoknya habis. Bisa notif kalau stok masuk?',
                    'a' => 'Fitur notif stok belum tersedia. Saran: cek berkala atau chat penjual via tombol <strong>Chat Penjual</strong> di halaman produk untuk tanya kapan restock.',
                ],
            ],
            'pembayaran' => [
                [
                    'q' => 'Metode pembayaran apa saja yang tersedia?',
                    'a' => '<strong>Via Midtrans Payment Gateway:</strong><br>
                          • Virtual Account (BCA, BRI, BNI, Mandiri, Permata, CIMB, dll)<br>
                          • E-Wallet (GoPay, ShopeePay, Dana, OVO, LinkAja, Sakuku)<br>
                          • Retail/Minimarket (Alfamart, Indomaret via VA)<br>
                          • Kartu Kredit/Debit (Visa, Mastercard, JCB)<br>
                          • QRIS<br><br>
                          <strong>Manual Transfer:</strong> Rekening toko (BCA/BRI/Mandiri) - bukti bayar wajib diupload.<br>
                          <strong>COD:</strong> Bayar di tempat (area & nominal terbatas).',
                ],
                [
                    'q' => 'Berapa batas waktu pembayaran?',
                    'a' => '<strong>Midtrans (VA/E-Wallet/QRIS/Kartu):</strong> Ada timer real-time (biasanya 15-24 jam).<br>
                          <strong>Transfer Manual:</strong> 10 menit setelah checkout (timer di halaman Status Bayar).<br>
                          <strong>COD:</strong> Bayar saat kurir sampai.',
                ],
                [
                    'q' => 'Sudah bayar tapi status belum berubah jadi "Dibayar".',
                    'a' => '<strong>Midtrans:</strong> Biasanya otomatis < 5 menit. Jika > 1 jam, cek email Midtrans/notif WhatsApp.<br>
                          <strong>Transfer Manual:</strong> Admin verifikasi manual (jam kerja 08-22 WIB). Pastikan bukti JPG/PNG < 2MB, nominal & nomor rekening terlihat jelas.<br>
                          <strong>COD:</strong> Status update setelah kurir konfirmasi pembayaran.',
                ],
                [
                    'q' => 'Bisa bayar cicilan?',
                    'a' => 'Ya, via Midtrans dengan Kartu Kredit (biasanya 3/6/12 bln) atau Akulata/Kredivo (pilih di halaman pembayaran Midtrans). Syarat & bunga mengikuti kebijakan masing-masing penyedia.',
                ],
            ],
            'pengiriman' => [
                [
                    'q' => 'Berapa lama estimasi pengiriman?',
                    'a' => 'Tergantung asal toko & kota tujuan:<br>
                          • <strong>Jawa & Bali:</strong> 1-3 hari kerja<br>
                          • <strong>Sumatra:</strong> 2-4 hari kerja<br>
                          • <strong>Kalimantan/Sulawesi/NTB/NTT:</strong> 3-6 hari kerja<br>
                          • <strong>Papua/Maluku:</strong> 5-10 hari kerja<br><br>
                          <em>Estimasi ini tidak mengikat & tidak termasuk hari libur/force majeure.</em>',
                ],
                [
                    'q' => 'Cara cek nomor resi & tracking?',
                    'a' => '1) Buka menu <strong>Pengiriman</strong> di navbar (ikon truk).<br>
                          2) Klik "Lacak" pada pesanan.<br>
                          3) Atau klik link WhatsApp/Email notif pengiriman.<br>
                          Nomor resi juga terlihat di detail pesanan (menu Riwayat Beli).',
                ],
                [
                    'q' => 'Paket tertulis "Diterima" tapi saya belum terima.',
                    'a' => '1) Cek ke tetangga/RT/security/lokasi penitipan kurir.<br>
                          2) Tanya ke kurir via nomor telepon di detail tracking.<br>
                          3) Jika 1x24 jam tidak ketemu: hubungi kami via Chat/WA dengan nomor resi & bukti tidak terima (foto CCTV/skck RT). Kami akan klaim ke ekspedisi.',
                ],
                [
                    'q' => 'Bisa ganti alamat pengiriman setelah dikirim?',
                    'a' => 'Tidak bisa via sistem. Hubungi <strong>langsung kurir</strong> via nomor telepon di tracking. Beberapa ekspedisi izinkan ganti alamat (bisa ada biaya tambahan).',
                ],
            ],
            'retur' => [
                [
                    'q' => 'Syarat retur/produk diganti?',
                    'a' => '<strong>Diterima jika:</strong> Rusak/pecah, cacat pabrik, salah kirim (warna/ukuran/model), kedaluwarsa.<br>
                          <strong>Batas waktu:</strong> Maksimal <strong>2 hari</strong> setelah terima (berdasarkan bukti terima ekspedisi).<br>
                          <strong>Kondisi:</strong> Asli, tidak dipakai, tag/packaging utuh, lengkap aksesoris.',
                ],
                [
                    'q' => 'Cara ajukan retur?',
                    'a' => '1) Foto/video bukti kerusakan/ketidaksesuaian (wajib jelas).<br>
                          2) Buka menu <strong>Pengiriman</strong> → Klik "Ajukan Retur" pada pesanan.<br>
                          3) Atau Chat Penjual langsung dari halaman Detail Produk/Riwayat.<br>
                          4) Tunggu verifikasi (max 1x24 jam) → Dapatkan instruksi & alamat pengembalian.',
                ],
                [
                    'q' => 'Siapa bayar ongkir retur?',
                    'a' => '<strong>Kesalahan toko/ekspedisi</strong> (rusak, salah kirim, cacat): <strong>Toko bayar</strong> (kita kirim label return/transfer ongkir).<br>
                          <strong>Alasan pribadi</strong> (salah pilih, tidak suka, ukuran tidak pas): <strong>Pembeli bayar</strong> ongkir bolak-balik.',
                ],
                [
                    'q' => 'Produk Flash Sale / Promo bisa diretur?',
                    'a' => 'Hanya jika <strong>rusak/cacat/salah kirim</strong>. Retur alasan pribadi (tidak suka, salah ukuran) <strong>tidak diterima</strong> untuk produk Flash Sale/Promo/Bundle. Cek deskripsi & tabel ukuran sebelum beli.',
                ],
            ],
            'akun' => [
                [
                    'q' => 'Lupa password, gimana reset?',
                    'a' => 'Klik <strong>"Lupa Password"</strong> di halaman Login → Masukkan email terdaftar → Cek email (cek folder Spam) → Klik link reset (berlaku 60 menit) → Buat password baru.',
                ],
                [
                    'q' => 'Gimana ganti email/nomor HP?',
                    'a' => 'Login → Menu <strong>Profil Saya</strong> → Edit Profil → Ganti email/nomor HP → Simpan. Verifikasi OTP akan dikirim ke kontak baru.',
                ],
                [
                    'q' => 'Bisa hapus akun?',
                    'a' => 'Hubungi admin via WA/Email dengan permintaan hapus akun. Data transaksi (riwayat, nota, garansi) akan diarsipkan sesuai regulasi (min 5 tahun).',
                ],
                [
                    'q' => 'Notifikasi WA/Email tidak masuk.',
                    'a' => '• Cek folder Spam/Promotions (email).<br>
                          • Pastikan nomor WA aktif & tidak block nomor kami.<br>
                          • Cek pengaturan notifikasi di aplikasi WA.<br>
                          • Data kontak di Profil sudah benar?',
                ],
            ],
        ];

        $urutan = 0;
        foreach ($faqs as $kategori => $items) {
            foreach ($items as $item) {
                DB::table('template_faq')->insert([
                    'kategori' => $kategori,
                    'pertanyaan' => $item['q'],
                    'jawaban' => $item['a'],
                    'urutan' => $urutan++,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected function seedSyaket(): void
    {
        // Kosongkan template_syaket dulu agar idempoten
        DB::table('template_syaket')->truncate();

        $konten = [
            [
                'tipe' => 'intro',
                'judul' => null,
                'isi' => 'Mohon baca dengan seksama sebelum melakukan transaksi.',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Informasi Produk',
                'isi' => 'Kami berusaha untuk menampilkan informasi produk (deskripsi, harga, gambar, spesifikasi) seakurat mungkin. Namun, kesalahan pengetikan, ketidaksesuaian warna akibat tampilan layar, atau perubahan spesifikasi tanpa pemberitahuan sebelumnya dapat terjadi.<br><ul><li>Gambar produk bersifat ilustrasi, warna &amp; detail bisa berbeda nyata.</li><li>Harga &amp; ketersediaan stok dapat berubah sewaktu-waktu.</li><li>Spesifikasi teknis mengacu pada data pabrikan/resmi.</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Pemesanan',
                'isi' => '<ul><li>Pemesanan dilakukan melalui sistem keranjang belanja di website.</li><li>Pembeli wajib mengisi data pemesanan (nama, alamat, nomor telepon) dengan benar, lengkap, dan dapat dihubungi.</li><li>Data pemesanan yang tidak valid/menyesatkan dapat menyebabkan pembatalan pesanan oleh sistem.</li><li>Kami berhak membatalkan pesanan apabila terjadi pelanggaran, penyalahgunaan sistem, atau kecurangan.</li><li>Pesanan otomatis dibatalkan jika pembayaran tidak dikonfirmasi dalam batas waktu (10 menit untuk transfer manual, sesuai timer Midtrans untuk payment gateway).</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Pembayaran',
                'isi' => 'Pembayaran dapat dilakukan melalui metode yang tersedia di halaman checkout:<ul><li><strong>Transfer Bank / Virtual Account / E-Wallet:</strong> Via Midtrans Payment Gateway (BCA, BRI, BNI, Mandiri, Permata, dll + GoPay, ShopeePay, Dana, OVO, LinkAja).</li><li><strong>COD (Bayar di Tempat):</strong> Tersedia untuk area tertentu dengan batasan nilai transaksi.</li><li>Bukti pembayaran (transfer manual) wajib diunggah ke halaman <em>Status Bayar</em> untuk verifikasi admin.</li><li>Pembayaran harus dilakukan dalam mata uang Rupiah (IDR).</li><li>Biaya administrasi/transfer (jika ada) ditanggung pembeli.</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Pengiriman',
                'isi' => '<ul><li>Pengiriman dilakukan setelah pembayaran <strong>terverifikasi &amp; dikonfirmasi</strong> oleh admin/toko.</li><li>Waktu pengiriman tergantung lokasi tujuan, jasa ekspedisi (JNE, J&amp;T, SiCepat, Ninja, Lokal), dan kondisi cuaca/keamanan.</li><li>Estimasi waktu (ETD) yang ditampilkan hanyalah perkiraan, bukan jaminan pasti.</li><li>Nomor resi &amp; link tracking akan dikirimkan ke WhatsApp/email &amp; tersedia di menu <em>Pengiriman</em>.</li><li>Jika paket rusak/hilang di jalan, klaim diajukan ke ekspedisi terkait (kami bantu prosesnya).</li><li>Alamat pengiriman tidak bisa diubah setelah status <strong>Dikirim</strong>.</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Retur, Pengembalian & Penggantian',
                'isi' => '<ul><li><strong>Retur Diterima Jika:</strong> Produk rusak/pecah/cacat pabrik, tidak sesuai pesanan (warna/ukuran/model salah dikirim), atau kedaluwarsa (untuk produk konsumsi).</li><li><strong>Batas Waktu:</strong> Maksimal <strong>2 x 24 jam</strong> setelah produk diterima (berdasarkan bukti terima ekspedisi).</li><li><strong>Syarat Produk:</strong> Dalam kondisi asli, tidak digunakan, tag/label/packaging utuh, lengkap aksesorisnya.</li><li><strong>Proses:</strong> Foto/video bukti kerusakan/ketidaksesuaian → Ajukan via menu <em>Pengiriman</em> / Chat Penjual → Verifikasi → Instruksi pengembalian.</li><li><strong>Biaya Retur:</strong> Dibatalkan oleh toko jika kesalahan dari toko/ekspedisi; ditanggung pembeli jika alasan pribadi (salah pilih, tidak suka, dll).</li><li><strong>Penggantian:</strong> Prioritas pengiriman ulang barang yang sama; jika stok habis → pengembalian dana full.</li><li>Produk <strong>tidak dapat diretur</strong>: Produk custom/personalisasi, produk higiene (celana dalam, masker, dll), produk digital/voucher, produk promo/flash sale (kecuali rusak/keliru).</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Garansi Produk',
                'isi' => '<ul><li>Garansi resmi mengikuti kebijakan pabrikan/distributor resmi masing-masing brand.</li><li>Klaim garansi memerlukan nota/resi pembelian asli &amp; barang dalam kondisi sesuai ketentuan garansi.</li><li>Garansi tidak berlaku untuk kerusakan akibat salah penggunaan, jatuh, cairan, modifikasi, bencana alam, atau normal wear &amp; tear.</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Privasi & Data Pribadi',
                'isi' => 'Data pribadi Anda (nama, alamat, telepon, email, koordinat lokasi) hanya digunakan untuk:<ul><li>Proses pemesanan, pembayaran, pengiriman, &amp; komunikasi terkait transaksi.</li><li>Peningkatan layanan, analitik, &amp; keamanan (fraud prevention).</li><li>Kepatuhan hukum &amp; peraturan perundang-undangan.</li></ul>Kami tidak menjual data pribadi ke pihak ketiga. Detail kebijakan privasi tercantum pada pasal ini.',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Hak Kekayaan Intelektual',
                'isi' => 'Semua konten di website (logo, nama brand, desain UI, foto produk, deskripsi, kode program) adalah hak milik toko atau mitra/reseller yang berhak. Dilarang menyalin, mendistribusikan, atau memodifikasi tanpa izin tertulis.',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Batas Tanggung Jawab',
                'isi' => 'Kami tidak bertanggung jawab atas:<ul><li>Keterlambatan/pengiriman gagal akibat force majeure, kebijakan pemerintah, gangguan jaringan ekspedisi.</li><li>Kerusakan/kerugian akibat penggunaan produk tidak sesuai manual/bukit petunjuk.</li><li>Ketersediaan/performa website 100% uptime (meski kami berusaha maksimal).</li></ul>',
            ],
            [
                'tipe' => 'pasal',
                'judul' => 'Perubahan Syarat & Ketentuan',
                'isi' => 'Kami berhak mengubah Syarat &amp; Ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya. Versi terbaru akan selalu ditampilkan di halaman ini dengan tanggal pembaruan. Penggunaan berkelanjutan setelah perubahan berarti Anda menyetujui revisi tersebut.',
            ],
        ];

        foreach ($konten as $urutan => $bagian) {
            DB::table('template_syaket')->insert([
                'tipe' => $bagian['tipe'],
                'judul' => $bagian['judul'],
                'isi' => $bagian['isi'],
                'urutan' => $urutan,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}