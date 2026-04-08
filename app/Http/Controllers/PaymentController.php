<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Ambil dari request biasa dulu (Trik Localhost)
            $transactionStatus = $request->transaction_status;
            $orderIdLengkap = $request->order_id;

            // Jika tidak ada di request, berarti dipanggil asli oleh Midtrans (Webhook Asli)
            if (!$transactionStatus || !$orderIdLengkap) {
                $notification = new Notification();
                $transactionStatus = $notification->transaction_status;
                $orderIdLengkap = $notification->order_id;
            }

            // Potong string "ORDER-" untuk mendapatkan ID Pesanan aslinya (12)
            $pesananId = str_replace('ORDER-', '', $orderIdLengkap);

            // Cari data pesanan di Database
            $pesanan = Pesanan::find($pesananId);

            if (!$pesanan) {
                return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            }

            // Cek status bayar dari Midtrans
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement' || $transactionStatus == 'success') {
                // UPDATE STATUS JADI LUNAS
                $pesanan->update(['status_bayar' => 1]);

            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // PESANAN GAGAL/KADALUARSA
                $pesanan->update(['status_bayar' => 2]); // Atau status gagal
            } elseif ($transactionStatus == 'pending') {
                // MASIH MENUNGGU TRANSFER
                $pesanan->update(['status_bayar' => 0]);
            }

            return response()->json(['message' => 'Status Pesanan Berhasil Diupdate']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}