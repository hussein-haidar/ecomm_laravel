@extends('layouts.template')

@section('content')
    <style>
        .chat-box {
            height: 420px;
            overflow-y: auto;
            background: #f7f7f7;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 15px;
        }

        .chat-bubble-wrap {
            margin-bottom: 12px;
        }

        .chat-bubble-wrap.me {
            text-align: right;
        }

        .chat-bubble-wrap.them {
            text-align: left;
        }

        .chat-bubble {
            display: inline-block;
            max-width: 75%;
            padding: 10px 12px;
            border-radius: 12px;
            word-break: break-word;
        }

        .chat-bubble.me {
            background: #3c8dbc;
            color: #fff;
            border-bottom-right-radius: 2px;
        }

        .chat-bubble.them {
            background: #fff;
            color: #333;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 2px;
        }

        .chat-empty {
            padding: 60px 20px;
            text-align: center;
            color: #999;
        }

        @media (max-width: 991px) {
            .chat-bubble {
                max-width: 92%;
            }
        }
    </style>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user"></i> {{ $chat_aktif->nama_pelanggan }}
                <small>{{ $chat_aktif->nama_produk ?: 'Produk umum' }}</small>
            </h3>
            <div class="box-tools pull-right">
                <a href="{{ route('admin_data.chat') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Kembali ke Inbox
                </a>
            </div>
        </div>

        <div class="box-body">
            <div id="chat-box" class="chat-box">
                <div class="text-center text-muted">
                    <i class="fa fa-spinner fa-spin"></i> Memuat pesan...
                </div>
            </div>

            <form id="form-chat" style="margin-top: 15px;">
                @csrf
                <input type="hidden" name="id_chat" value="{{ $chat_aktif->id_chat }}">
                <div class="input-group">
                    <input type="text" name="pesan" id="input-pesan" class="form-control"
                        placeholder="Ketik balasan untuk pelanggan..." maxlength="1000" autocomplete="off">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane"></i> Kirim
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <script>
        const activeChatId = @json($chat_aktif->id_chat);
        const pesanUrlTemplate = @json(route('admin_data.chatPesan', ['id_chat' => '__ID__']));
        const kirimUrl = @json(route('admin_data.chatKirim'));
        const csrfToken = @json(csrf_token());

        function escHtml(text) {
            const div = document.createElement('div');
            div.textContent = text === null || text === undefined ? '' : String(text);
            return div.innerHTML;
        }

        function renderMessages(messages) {
            const chatBox = document.getElementById('chat-box');

            if (!messages.length) {
                chatBox.innerHTML =
                    '<div class="chat-empty"><i class="fa fa-inbox fa-2x"></i><p style="margin-top:10px;">Belum ada pesan pada chat ini.</p></div>';
                return;
            }

            let html = '';
            messages.forEach(function(message) {
                const isMe = message.pengirim === 'admin';
                const wrapperClass = isMe ? 'me' : 'them';
                const bubbleClass = isMe ? 'me' : 'them';
                const labelClass = isMe ? 'label-success' : 'label-primary';
                const roleLabel = isMe ? 'Admin' : 'Pelanggan';

                html += '<div class="chat-bubble-wrap ' + wrapperClass + '">';
                html += '<div class="chat-bubble ' + bubbleClass + '">';
                html += '<div style="margin-bottom:6px;">';
                html += '<strong>' + escHtml(message.nama || 'Pengguna') + '</strong> ';
                html += '<span class="label ' + labelClass + '">' + roleLabel + '</span>';
                html += '</div>';
                html += '<div>' + escHtml(message.pesan) + '</div>';
                html += '<div style="font-size:11px;margin-top:6px;opacity:0.85;">' + escHtml(message.waktu) + '</div>';
                html += '</div>';
                html += '</div>';
            });

            chatBox.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function tampilkanGagalMuat() {
            document.getElementById('chat-box').innerHTML =
                '<div class="chat-empty"><i class="fa fa-warning fa-2x"></i><p style="margin-top:10px;">Pesan gagal dimuat.</p></div>';
        }

        function muatPesan() {
            const pesanUrl = pesanUrlTemplate.replace('__ID__', String(activeChatId));

            fetch(pesanUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (!data.success) {
                        throw new Error(data.message || 'Gagal memuat pesan.');
                    }

                    renderMessages(data.messages || []);
                })
                .catch(function() {
                    tampilkanGagalMuat();
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-chat');
            const inputPesan = document.getElementById('input-pesan');

            muatPesan();
            setInterval(muatPesan, 3000);

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const pesan = inputPesan.value.trim();
                if (!pesan) {
                    return;
                }

                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('id_chat', activeChatId);
                formData.append('pesan', pesan);

                fetch(kirimUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal mengirim pesan.');
                        }

                        inputPesan.value = '';
                        muatPesan();
                    })
                    .catch(function(error) {
                        alert(error.message || 'Gagal mengirim pesan.');
                    });
            });
        });
    </script>
@endsection
