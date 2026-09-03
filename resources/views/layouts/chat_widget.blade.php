@if (!empty($user_logged_in))
    <style>
        #chat-widget-toggle {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 9999;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }

        #chat-widget-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            font-size: 11px;
            min-width: 20px;
            height: 20px;
            line-height: 20px;
            border-radius: 10px;
            padding: 0 5px;
        }

        #chat-widget-panel {
            position: fixed;
            right: 20px;
            bottom: 90px;
            z-index: 9999;
            width: 380px;
            max-width: calc(100vw - 40px);
            display: none;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            overflow: hidden;
        }

        #chat-widget-panel.open {
            display: block;
        }

        #chat-widget-panel .card {
            border-radius: 12px;
            border: none;
        }

        #chat-widget-panel .chat-widget-header {
            background: #0d6efd;
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 12px;
        }

        #chat-widget-list {
            height: 440px;
            overflow-y: auto;
        }

        #chat-widget-messages {
            height: 360px;
            overflow-y: auto;
            background: #f8f9fa;
            padding: 10px;
        }

        #chat-widget-thread-header {
            background: #e9ecef;
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }

        #chat-widget-form {
            padding: 8px;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }

        .chat-widget-item {
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .chat-widget-item:hover {
            background: #f1f3f5;
        }

        .chat-widget-item.active {
            background: #e7f1ff;
        }
    </style>

    <div id="chat-widget">
        <button type="button" id="chat-widget-toggle" class="btn btn-primary" title="Chat Penjual">
            <i class="fas fa-comments"></i>
            <span id="chat-widget-badge" class="badge bg-danger d-none">0</span>
        </button>

        <div id="chat-widget-panel">
            <div class="card">
                <div class="card-header chat-widget-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-comments me-1"></i> Chat Penjual</span>
                    <button type="button" class="btn-close btn-close-white" id="chat-widget-close"
                        aria-label="Tutup"></button>
                </div>

                <div id="chat-widget-list"></div>

                <div id="chat-widget-thread" class="d-none">
                    <div id="chat-widget-thread-header"
                        class="chat-widget-thread-header d-flex justify-content-between align-items-center">
                        <div class="text-truncate">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                id="chat-widget-back" title="Kembali">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <strong id="chat-widget-thread-title"></strong>
                        </div>
                    </div>
                    <div id="chat-widget-messages"></div>
                    <form id="chat-widget-form" class="chat-widget-form d-flex gap-1">
                        <input type="text" id="chat-widget-input" class="form-control form-control-sm"
                            placeholder="Tulis pesan..." maxlength="1000" autocomplete="off">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            if (window.chatWidgetInited) return;
            window.chatWidgetInited = true;

             var urls = {
                daftar: '{{ route('pelanggan_data.chatDaftar') }}',
                buka: '{{ route('pelanggan_data.chatBuka') }}',
                pesan: '{{ route('pelanggan_data.chatPesan', ['id_chat' => '__ID__']) }}',
                kirim: '{{ route('pelanggan_data.chatKirim') }}'
            };
            var csrfToken = '{{ csrf_token() }}';

            var toggle = document.getElementById('chat-widget-toggle');
            var panel = document.getElementById('chat-widget-panel');
            var closeBtn = document.getElementById('chat-widget-close');
            var badge = document.getElementById('chat-widget-badge');
            var listEl = document.getElementById('chat-widget-list');
            var threadEl = document.getElementById('chat-widget-thread');
            var threadHeader = document.getElementById('chat-widget-thread-header');
            var threadTitle = document.getElementById('chat-widget-thread-title');
            var messagesEl = document.getElementById('chat-widget-messages');
            var formEl = document.getElementById('chat-widget-form');
            var inputEl = document.getElementById('chat-widget-input');
            var backBtn = document.getElementById('chat-widget-back');

            var chatAktifId = null;
            var chatAktifNama = '';
            var pollPesanTimer = null;
            var pollDaftarTimer = null;

            function escHtml(str) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;');
            }

            function bukaPanel() {
                panel.classList.add('open');
                muatDaftar();
            }

            function tutupPanel() {
                panel.classList.remove('open');
                hentikanPesan();
            }

            function muatDaftar() {
                fetch(urls.daftar)
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (!res.success) return;
                        var daftar = res.daftar_chat || [];
                        var total = res.total_belum_dibaca || 0;
                        badge.textContent = total;
                        badge.classList.toggle('d-none', total === 0);
                        var navBadge = document.getElementById('chat-nav-badge');
                        if (navBadge) {
                            navBadge.textContent = total;
                            navBadge.classList.toggle('d-none', total === 0);
                        }
                        if (!panel.classList.contains('open')) return;

                        var html = '';
                        if (daftar.length === 0) {
                            html = '<div class="text-center text-muted py-5 px-3">' +
                                'Belum ada chat.<br>Mulai chat lewat tombol "Chat Penjual" di produk atau keranjang.</div>';
                        } else {
daftar.forEach(function(c) {
                var aktif = chatAktifId && chatAktifId == c.id_chat;
                var produk = c.nama_produk ?
                    '<span class="badge bg-info text-dark">' + escHtml(c.nama_produk) +
                    '</span>' :
                    '<span class="badge bg-secondary">Chat umum toko</span>';
                var belumDibaca = c.belum_dibaca > 0 ?
                    '<span class="badge bg-danger ms-1">' + c.belum_dibaca + '</span>' : '';
                var lastPesan = c.last_pesan ? c.last_pesan : 'Belum ada pesan';
                var senderBadge = c.last_pesan ?
                    '<span class="badge bg-primary me-1">' + escHtml(c.nama_pengirim || '') + '</span>' : '';
                html += '<div class="chat-widget-item p-2 ' + (aktif ? 'active' : '') +
                    '" data-id="' + c.id_chat + '">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '<strong class="text-truncate">' + escHtml(c.nama_toko) + '</strong>' +
                    '<small class="text-nowrap ms-2 text-muted">' + escHtml(c.last_waktu) +
                    '</small></div>' +
                    '<div class="small">' + produk + belumDibaca + '</div>' +
                    '<div class="small text-truncate text-muted">' + senderBadge + ' ' + escHtml(lastPesan) + '</div>' +
                    '</div>';
            });
                        }
                        listEl.innerHTML = html;
                        listEl.querySelectorAll('.chat-widget-item').forEach(function(el) {
                            el.addEventListener('click', function() {
                                var id = this.getAttribute('data-id');
                                var nama = this.querySelector('strong').textContent;
                                bukaThread(id, nama);
                            });
                        });
                    })
                    .catch(function() {});
            }

            function bukaThread(idChat, namaToko) {
                chatAktifId = idChat;
                chatAktifNama = namaToko;
                listEl.classList.add('d-none');
                threadEl.classList.remove('d-none');
                threadTitle.textContent = namaToko;
                messagesEl.innerHTML = '<div class="text-center text-muted py-5">Memuat pesan...</div>';
                muatPesan();
                if (pollPesanTimer) clearInterval(pollPesanTimer);
                pollPesanTimer = setInterval(muatPesan, 3000);
            }

            function kembaliKeDaftar() {
                hentikanPesan();
                chatAktifId = null;
                threadEl.classList.add('d-none');
                listEl.classList.remove('d-none');
                muatDaftar();
            }

            function hentikanPesan() {
                if (pollPesanTimer) {
                    clearInterval(pollPesanTimer);
                    pollPesanTimer = null;
                }
            }

            function muatPesan() {
                if (!chatAktifId) return;
                fetch(urls.pesan.replace('__ID__', String(chatAktifId)))
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (!res.success) return;
                        var html = '';
                        res.messages.forEach(function(m) {
                            var dariPemilik = m.pengirim !== 'pelanggan';
                            var bubble = dariPemilik ? 'bg-white border' : 'bg-primary text-white';
                            html += '<div class="d-flex mb-2 ' + (dariPemilik ? 'justify-content-start' :
                                'justify-content-end') + '">';
                            html += '<div class="p-2 rounded ' + bubble + '" style="max-width:75%;">';
                            html += '<div class="small ' + (dariPemilik ? 'text-muted' : 'text-white-50') + '">' +
                                escHtml(m.nama) + ' &middot; ' + escHtml(m.waktu) + '</div>';
                            html += '<div class="text-break">' + escHtml(m.pesan) + '</div>';
                            html += '</div></div>';
                        });
                        if (!html) {
                            html = '<div class="text-center text-muted py-5">Belum ada pesan. Tanyakan produk Anda di sini.</div>';
                        }
                        messagesEl.innerHTML = html;
                        messagesEl.scrollTop = messagesEl.scrollHeight;
                    })
                    .catch(function() {});
            }

            function bukaChatBaru(namaToko, idStok) {
                bukaPanel();
                var fd = new FormData();
                fd.append('nama_toko', namaToko);
                fd.append('id_stok', idStok || '');
                fd.append('_token', csrfToken);
                fetch(urls.buka, { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success && res.chat) {
                            bukaThread(res.chat.id_chat, res.chat.nama_toko);
                        }
                    })
                    .catch(function() {});
            }

            window.bukaChat = function(namaToko, idStok) {
                if (namaToko) {
                    bukaChatBaru(namaToko, idStok);
                } else {
                    bukaPanel();
                }
            };

            toggle.addEventListener('click', function() {
                if (panel.classList.contains('open')) {
                    tutupPanel();
                } else {
                    bukaPanel();
                }
            });

            closeBtn.addEventListener('click', tutupPanel);
            backBtn.addEventListener('click', kembaliKeDaftar);

            formEl.addEventListener('submit', function(e) {
                e.preventDefault();
                var pesan = inputEl.value.trim();
                if (!pesan || !chatAktifId) return;
                var fd = new FormData();
                fd.append('id_chat', chatAktifId);
                fd.append('pesan', pesan);
                fd.append('_token', csrfToken);
                fetch(urls.kirim, { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        inputEl.value = '';
                        if (res.success) muatPesan();
                    })
                    .catch(function() {});
            });

            pollDaftarTimer = setInterval(muatDaftar, 15000);
            muatDaftar();
        })();
    </script>
@endif
