<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\M_Pembayaran;

class MidtransCallbackController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->input('order_id') . $request->input('status_code') . $request->input('gross_amount') . $serverKey);

        if ($hashed !== $request->input('signature_key')) {
            Log::warning('Midtrans callback: invalid signature');
            return response('Invalid signature', 403);
        }

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        $statusCode = $request->input('status_code');

        $pembayaran = DB::table('pembayaran')
            ->where('midtrans_order_id', $orderId)
            ->first();

        if (!$pembayaran) {
            Log::warning('Midtrans callback: order not found - ' . $orderId);
            return response('Order not found', 404);
        }

        $newStatus = null;

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $newStatus = 'Dibayar';
            }
        } elseif ($transactionStatus == 'settlement') {
            $newStatus = 'Dibayar';
        } elseif ($transactionStatus == 'pending') {
            $newStatus = 'Belum Bayar';
        } elseif ($transactionStatus == 'deny') {
            $newStatus = 'Dibatalkan';
        } elseif ($transactionStatus == 'expire') {
            $newStatus = 'Dibatalkan';
        } elseif ($transactionStatus == 'cancel') {
            $newStatus = 'Dibatalkan';
        }

        if ($newStatus) {
            $updateData = [
                'midtrans_status' => $transactionStatus,
                'midtrans_response' => json_encode($request->all()),
            ];

            if ($newStatus == 'Dibayar') {
                $updateData['status_bayar'] = 'Dibayar';
                $updateData['waktu_pembayaran'] = now()->format('Y-m-d H:i:s');
            } elseif ($newStatus == 'Dibatalkan') {
                $updateData['status_bayar'] = 'Dibatalkan';
            }

            DB::table('pembayaran')
                ->where('id_bayar', $pembayaran->id_bayar)
                ->update($updateData);

            if ($newStatus == 'Dibayar' && $pembayaran->status_bayar != 'Dibayar') {
                M_Pembayaran::konfirmasi_pembayaran($pembayaran->id_bayar, $pembayaran->nama_pelanggan);

                DB::table('pembelian')
                    ->where('id_bayar', $pembayaran->id_bayar)
                    ->update(['status_beli' => 'Berhasil']);
            } elseif ($newStatus == 'Dibatalkan' && $pembayaran->status_bayar != 'Dibatalkan') {
                // Restore stok saat pembayaran dibatalkan
                $pembelianList = DB::table('pembelian')
                    ->where('id_bayar', $pembayaran->id_bayar)
                    ->where('status_beli', 'Ditunda')
                    ->get();
                
                $stokModel = new \App\Models\M_Stok();
                foreach ($pembelianList as $pb) {
                    if ($pb->id_stok) {
                        $stokModel->tambahStok($pb->id_stok, $pb->jumlah_produk);
                    }
                }
                
                DB::table('pembelian')
                    ->where('id_bayar', $pembayaran->id_bayar)
                    ->where('status_beli', 'Ditunda')
                    ->update(['status_beli' => 'Dibatalkan']);
                
                // Delete cart items
                DB::table('keranjang')
                    ->where('nama_pelanggan', $pembayaran->nama_pelanggan)
                    ->where('status_keranjang', 'Selesai')
                    ->delete();
                
                Log::info('Payment cancelled, stok restored: ' . $orderId);
            }

            // Kirim notifikasi ke pelanggan
            if ($pembayaran->id_pelanggan) {
                $judul = $newStatus == 'Dibayar' ? 'Pembayaran Berhasil' : 'Pembayaran Dibatalkan';
                $pesan = $newStatus == 'Dibayar'
                    ? 'Pembayaran untuk order ' . $orderId . ' telah berhasil dikonfirmasi.'
                    : 'Pembayaran untuk order ' . $orderId . ' telah dibatalkan.';
                $tipe = $newStatus == 'Dibayar' ? 'sukses' : 'peringatan';
                
                DB::table('notifikasi')->insert([
                    'id_pelanggan' => $pembayaran->id_pelanggan,
                    'nama_pelanggan' => $pembayaran->nama_pelanggan,
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'tipe' => $tipe,
                    'link' => route('pelanggan_data.statusBayar'),
                    'waktu' => now(),
                ]);
            }

            Log::info('Midtrans callback processed: order=' . $orderId . ' status=' . $newStatus);
        }

        return response('OK', 200);
    }

    public function finish(Request $request)
    {
        return redirect()->route('pelanggan_data.statusBayar')
            ->with('pesan_beli', 'Pembayaran berhasil diproses!');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('pelanggan_data.statusBayar')
            ->with('error', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    public function error(Request $request)
    {
        return redirect()->route('pelanggan_data.statusBayar')
            ->with('error', 'Terjadi kesalahan saat pemrosesan pembayaran.');
    }

    public function checkStatus($id_bayar)
    {
        $pembayaran = DB::table('pembayaran')
            ->where('id_bayar', $id_bayar)
            ->first();

        if (!$pembayaran) {
            return response()->json(['found' => false]);
        }

        if ($pembayaran->status_bayar == 'Dibayar') {
            return response()->json([
                'found' => true,
                'status_bayar' => 'Dibayar',
            ]);
        }

        if (!empty($pembayaran->midtrans_order_id)) {
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');

                $statusResponse = \Midtrans\Transaction::status($pembayaran->midtrans_order_id);
                $transactionStatus = $statusResponse->transaction_status ?? null;

                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $alreadyPaid = DB::table('pembayaran')
                        ->where('id_bayar', $id_bayar)
                        ->where('status_bayar', 'Dibayar')
                        ->exists();

                    if (!$alreadyPaid) {
                        $updateData = [
                            'status_bayar' => 'Dibayar',
                            'midtrans_status' => $transactionStatus,
                            'midtrans_response' => json_encode($statusResponse),
                            'waktu_pembayaran' => now()->format('Y-m-d H:i:s'),
                        ];

                        DB::table('pembayaran')
                            ->where('id_bayar', $id_bayar)
                            ->update($updateData);

                        M_Pembayaran::konfirmasi_pembayaran($pembayaran->id_bayar, $pembayaran->nama_pelanggan);

                        DB::table('pembelian')
                            ->where('id_bayar', $id_bayar)
                            ->update(['status_beli' => 'Berhasil']);
                    }

                    return response()->json([
                        'found' => true,
                        'status_bayar' => 'Dibayar',
                    ]);
                } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                    DB::table('pembayaran')
                        ->where('id_bayar', $id_bayar)
                        ->update([
                            'status_bayar' => 'Dibatalkan',
                            'midtrans_status' => $transactionStatus,
                        ]);

                    return response()->json([
                        'found' => true,
                        'status_bayar' => 'Dibatalkan',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Midtrans checkStatus error for ' . $id_bayar . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'found' => true,
            'status_bayar' => $pembayaran->status_bayar,
        ]);
    }
}
