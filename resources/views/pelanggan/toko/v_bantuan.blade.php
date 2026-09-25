@extends('layouts.app')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const hash = this.getAttribute('href');
                if (hash.length <= 1) return;
                const target = document.querySelector(hash);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Auto-open FAQ from URL hash
        if (window.location.hash) {
            const target = document.querySelector(window.location.hash);
            if (target && target.classList.contains('accordion-collapse')) {
                const bsCollapse = new bootstrap.Collapse(target, { show: true });
            }
        }
    });
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            {{-- Header --}}
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h1 class="fw-bold mb-2">Bantuan / FAQ</h1>
                <p class="text-muted">Temukan jawaban atas pertanyaan umum seputar belanja di <strong>{{ $dataWebsite['nama_toko'] }}</strong></p>
            </div>

            {{-- Search FAQ --}}
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="faqSearch" class="form-control form-control-lg ps-5" placeholder="Cari pertanyaan... (contoh: pengiriman, pembayaran, retur)">
                    </div>
                </div>
            </div>

            {{-- FAQ Categories --}}
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-5" id="faqCategories">
                <button class="btn btn-primary rounded-pill px-4 active" data-category="all">Semua</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pemesanan">Pemesanan</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pembayaran">Pembayaran</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="pengiriman">Pengiriman</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="retur">Retur & Garansi</button>
                <button class="btn btn-outline-primary rounded-pill px-4" data-category="akun">Akun & Profil</button>
            </div>

            {{-- FAQ Accordion --}}
            <div class="accordion accordion-flush" id="faqAccordion">
                @foreach($faqs as $category => $items)
                    @foreach($items as $index => $faq)
                        <div class="accordion-item faq-item" data-category="{{ $category }}">
                            <h2 class="accordion-header" id="heading{{ ucfirst($category) }}{{ $index + 1 }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($category) }}{{ $index + 1 }}" aria-expanded="false" aria-controls="collapse{{ ucfirst($category) }}{{ $index + 1 }}">
                                    {{ $faq->pertanyaan }}
                                </button>
                            </h2>
                            <div id="collapse{{ ucfirst($category) }}{{ $index + 1 }}" class="accordion-collapse collapse" aria-labelledby="heading{{ ucfirst($category) }}{{ $index + 1 }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {!! $faq->jawaban !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- No Results Message --}}
            <div id="faqNoResults" class="text-center py-5 d-none">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Hasil</h5>
                <p class="text-muted">Coba kata kunci lain atau pilih kategori berbeda.</p>
            </div>

            {{-- Contact CTA --}}
            <div class="card border-0 shadow-sm mt-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-5 text-white text-center">
                    <h4 class="fw-bold mb-3">Masih Butuh Bantuan?</h4>
                    <p class="mb-4 opacity-75">Tim support kami siap bantu via WhatsApp atau Email.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="https://wa.me/{{ $dataWebsite['wa_pusat_link'] }}" target="_blank" class="btn btn-light rounded-pill px-4 fw-semibold">
                            <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                        <a href="mailto:{{ $dataWebsite['email_toko'] }}" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                            <i class="fas fa-envelope me-2"></i>Kirim Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // FAQ Search Filter
    const searchInput = document.getElementById('faqSearch');
    const categoryButtons = document.querySelectorAll('#faqCategories button');
    const faqItems = document.querySelectorAll('.faq-item');
    const noResults = document.getElementById('faqNoResults');

    let currentCategory = 'all';
    let searchTerm = '';

    function filterFAQs() {
        let visibleCount = 0;
        faqItems.forEach(item => {
            const category = item.dataset.category;
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            const text = question + ' ' + answer;

            const matchesCategory = currentCategory === 'all' || category === currentCategory;
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', function() {
        searchTerm = this.value.toLowerCase().trim();
        filterFAQs();
    });

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            categoryButtons.forEach(button => {
                button.classList.remove('active');
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            currentCategory = this.dataset.category;
            filterFAQs();
        });
    });
</script>
@endsection
