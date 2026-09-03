@extends('layouts.app')

@section('content')

<!-- Section -->
<main class="product-section">
 <div class="container mt-5 mb-5">
     <div class="card shadow">
         <div class="card-body">
             <h2 class="text-center mb-4">Bantuan / FAQ</h2>

             <div class="accordion" id="faqAccordion">

                 <div class="accordion-item">
                     <h2 class="accordion-header" id="headingOne">
                         <button class="accordion-button" type="button" data-bs-toggle="collapse"
                             data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                             1. Bagaimana cara melakukan pemesanan?
                         </button>
                     </h2>
                     <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                         data-bs-parent="#faqAccordion">
                         <div class="accordion-body">
                             Anda dapat melakukan pemesanan dengan memilih produk yang diinginkan, menambahkannya ke
                             keranjang, dan mengikuti proses checkout. Pastikan Anda telah login sebelum memesan.
                         </div>
                     </div>
                 </div>

                 <div class="accordion-item">
                     <h2 class="accordion-header" id="headingTwo">
                         <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                             data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                             2. Apakah saya perlu membuat akun untuk membeli produk?
                         </button>
                     </h2>
                     <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                         data-bs-parent="#faqAccordion">
                         <div class="accordion-body">
                             Ya, Anda harus mendaftar dan login ke akun Anda untuk dapat memesan produk dan melihat
                             riwayat transaksi.
                         </div>
                     </div>
                 </div>

                 <div class="accordion-item">
                     <h2 class="accordion-header" id="headingThree">
                         <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                             data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                             3. Bagaimana cara mengecek status pesanan saya?
                         </button>
                     </h2>
                     <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                         data-bs-parent="#faqAccordion">
                         <div class="accordion-body">
                             Anda dapat melihat status pesanan melalui menu “Riwayat Pemesanan” di halaman akun Anda.
                         </div>
                     </div>
                 </div>

                 <div class="accordion-item">
                     <h2 class="accordion-header" id="headingFour">
                         <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                             data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                             4. Apakah saya bisa mengembalikan produk?
                         </button>
                     </h2>
                     <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                         data-bs-parent="#faqAccordion">
                         <div class="accordion-body">
                             Ya, pengembalian hanya bisa dilakukan maksimal 2 hari setelah produk diterima dan dalam
                             kondisi belum digunakan. Silakan hubungi admin untuk proses retur.
                         </div>
                     </div>
                 </div>

                 <div class="accordion-item">
                     <h2 class="accordion-header" id="headingFive">
                         <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                             data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                             5. Bagaimana cara menghubungi layanan pelanggan?
                         </button>
                     </h2>
                     <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                         data-bs-parent="#faqAccordion">
                         <div class="accordion-body">
                             Anda dapat menghubungi kami melalui WhatsApp atau email yang tersedia di halaman Kontak Kami.
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
</div>
</main>

@endsection