<?php

namespace App\Http\Controllers;

use App\Models\M_FaqToko;
use App\Models\M_SyaketToko;
use App\Models\M_TemplateFaq;
use App\Models\M_TemplateSyaket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Konten_data extends Controller
{
    protected const KATEGORI_FAQ = ['pemesanan', 'pembayaran', 'pengiriman', 'retur', 'akun'];

    // ---- Helper internal ----

    protected function isSuperadmin(): bool
    {
        return session('level') === 'superadmin';
    }

    protected function routePrefix(): string
    {
        switch (session('level')) {
            case 'superadmin':
                return 'superadmin_data';
            case 'admin':
                return 'admin_data';
            default:
                return 'pemilik_data';
        }
    }

    protected function routesFor(string $jenis): array
    {
        $prefix = $this->routePrefix();
        $nama = $jenis === 'syaket' ? ($this->isSuperadmin() ? 'tpl_syaket' : 'syaket') : ($this->isSuperadmin() ? 'tpl_faq' : 'faq');
        return [
            'index'  => $prefix . '.' . $nama . '.index',
            'add'    => $prefix . '.' . $nama . '.add',
            'save'   => $prefix . '.' . $nama . '.save',
            'edit'   => $prefix . '.' . $nama . '.edit',
            'update' => $prefix . '.' . $nama . '.update',
            'delete' => $prefix . '.' . $nama . '.delete',
        ];
    }

    protected function baseData(string $title, string $jenis, array $extra = []): array
    {
        return array_merge([
            'title' => $title,
            'title2' => $title,
            'routes' => $this->routesFor($jenis),
        ], $extra);
    }

    // ===================== FAQ =====================

    public function index_faq(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $kategori = trim((string) $request->input('kategori'));

        if ($this->isSuperadmin()) {
            $model = new M_TemplateFaq();
            $query = $model->newQuery();
        } else {
            $model = new M_FaqToko();
            $query = $model->newQuery()->where('sesi_user', session('sesi_user'));
        }

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        $pkCol = $this->isSuperadmin() ? 'id_tpl_faq' : 'id_faq_toko';
        $data = $this->baseData('Data FAQ', 'faq', [
            'faq' => $query->orderBy('urutan', 'ASC')->orderBy($pkCol, 'ASC')->get(),
            'kategori_list' => self::KATEGORI_FAQ,
            'search' => $search,
            'filter_kategori' => $kategori,
        ]);
        return view('konten.faq.index', $data);
    }

    public function add_faq()
    {
        $data = $this->baseData('Tambah FAQ', 'faq', [
            'kategori_list' => self::KATEGORI_FAQ,
        ]);
        return view('konten.faq.create', $data);
    }

    public function save_faq(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:50',
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
        ]);

        $payload = [
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($this->isSuperadmin()) {
            M_TemplateFaq::create($payload);
        } else {
            $payload['sesi_user'] = session('sesi_user');
            M_FaqToko::create($payload);
        }

        Session::flash('pesan', 'Data FAQ berhasil ditambahkan!');
        return redirect()->route($this->routesFor('faq')['index']);
    }

    public function edit_faq($id)
    {
        $item = $this->isSuperadmin()
            ? (new M_TemplateFaq)->findOrFail($id)
            : (new M_FaqToko)->getFaqById($id);

        $data = $this->baseData('Edit FAQ', 'faq', [
            'faq' => $item,
            'kategori_list' => self::KATEGORI_FAQ,
        ]);
        return view('konten.faq.edit', $data);
    }

    public function update_faq(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:50',
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
        ]);

        $payload = [
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($this->isSuperadmin()) {
            M_TemplateFaq::where('id_tpl_faq', $id)->update($payload);
        } else {
            M_FaqToko::where('id_faq_toko', $id)
                ->where('sesi_user', session('sesi_user'))
                ->update($payload);
        }

        Session::flash('pesan', 'Data FAQ berhasil diperbarui!');
        return redirect()->route($this->routesFor('faq')['index']);
    }

    public function delete_faq($id)
    {
        if ($this->isSuperadmin()) {
            M_TemplateFaq::where('id_tpl_faq', $id)->delete();
        } else {
            M_FaqToko::where('id_faq_toko', $id)
                ->where('sesi_user', session('sesi_user'))
                ->delete();
        }

        Session::flash('pesan', 'Data FAQ berhasil dihapus!');
        return redirect()->route($this->routesFor('faq')['index']);
    }

    // ===================== SYARAT & KETENTUAN =====================

    public function index_syaket()
    {
        if ($this->isSuperadmin()) {
            $syaket = (new M_TemplateSyaket)->getSyaket();
        } else {
            $syaket = (new M_SyaketToko)->getSyaket();
        }

        $data = $this->baseData('Data Syarat & Ketentuan', 'syaket', [
            'syaket' => $syaket,
        ]);
        return view('konten.syaket.index', $data);
    }

    public function add_syaket()
    {
        $data = $this->baseData('Tambah Syarat & Ketentuan', 'syaket');
        return view('konten.syaket.create', $data);
    }

    public function save_syaket(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:intro,pasal',
            'judul' => 'nullable|string|max:255',
            'isi' => 'required|string',
        ]);

        $tipe = in_array($request->tipe, ['intro', 'pasal']) ? $request->tipe : 'pasal';

        $payload = [
            'tipe' => $tipe,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($this->isSuperadmin()) {
            M_TemplateSyaket::create($payload);
        } else {
            $payload['sesi_user'] = session('sesi_user');
            M_SyaketToko::create($payload);
        }

        Session::flash('pesan', 'Data Syarat & Ketentuan berhasil ditambahkan!');
        return redirect()->route($this->routesFor('syaket')['index']);
    }

    public function edit_syaket($id)
    {
        $item = $this->isSuperadmin()
            ? (new M_TemplateSyaket)->getSyaketById($id)
            : (new M_SyaketToko)->getSyaketById($id);

        $data = $this->baseData('Edit Syarat & Ketentuan', 'syaket', [
            'syaket' => $item,
        ]);
        return view('konten.syaket.edit', $data);
    }

    public function update_syaket(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|string|max:20',
            'judul' => 'nullable|string|max:255',
            'isi' => 'required|string',
        ]);

        $tipe = in_array($request->tipe, ['intro', 'pasal']) ? $request->tipe : 'pasal';

        $payload = [
            'tipe' => $tipe,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'urutan' => $request->urutan ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($this->isSuperadmin()) {
            M_TemplateSyaket::where('id_tpl_syaket', $id)->update($payload);
        } else {
            M_SyaketToko::where('id_syaket_toko', $id)
                ->where('sesi_user', session('sesi_user'))
                ->update($payload);
        }

        Session::flash('pesan', 'Data Syarat & Ketentuan berhasil diperbarui!');
        return redirect()->route($this->routesFor('syaket')['index']);
    }

    public function delete_syaket($id)
    {
        if ($this->isSuperadmin()) {
            M_TemplateSyaket::where('id_tpl_syaket', $id)->delete();
        } else {
            M_SyaketToko::where('id_syaket_toko', $id)
                ->where('sesi_user', session('sesi_user'))
                ->delete();
        }

        Session::flash('pesan', 'Data Syarat & Ketentuan berhasil dihapus!');
        return redirect()->route($this->routesFor('syaket')['index']);
    }
}