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
    });
</script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            {{-- Header --}}
            <div class="text-center mb-4" data-aos="fade-up">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h1 class="fw-bold mb-2">Syarat & Ketentuan</h1>
                <p class="text-muted">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            {{-- Terms Content (Intro + Pasal dalam satu dokumen) --}}
            <div class="card border rounded-4 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4 p-lg-5">
                    <div class="terms-content">
                        @php $noPasal = 0; @endphp
                        @foreach($syaket as $bagian)
                            @if($bagian->tipe === 'intro')
                                <div class="term-intro {{ $loop->last ? '' : 'mb-4 pb-4 border-bottom' }}">
                                    <p class="lead text-muted mb-0">
                                        Selamat datang di <strong>{{ $nama_toko }}</strong>. {!! $bagian->isi !!}
                                    </p>
                                </div>
                            @else
                                @php $noPasal++; @endphp
                                <section id="term-{{ $noPasal }}" class="{{ $loop->last ? 'mb-0' : 'mb-4 pb-4 border-bottom' }}">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">{{ $noPasal }}</span>
                                        <h3 class="fw-bold mb-0">{{ $bagian->judul }}</h3>
                                    </div>
                                    {!! $bagian->isi !!}
                                </section>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Contact CTA --}}
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-5 text-white">
                        <h4 class="fw-bold mb-3">Masih Punya Pertanyaan?</h4>
                        <p class="mb-4 opacity-75">Tim layanan pelanggan kami siap membantu Anda kapan saja.</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dataWebsite['wa_pusat'] ?? '6281234567890') }}" target="_blank" class="btn btn-light rounded-pill px-4 fw-semibold">
                                <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                            </a>
                            <a href="{{ route('home_toko.bantuan') }}" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                                <i class="fas fa-question-circle me-2"></i>Bantuan / FAQ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection