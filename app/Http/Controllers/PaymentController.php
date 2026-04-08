<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    // Peta payment_type Midtrans ke kode integer untuk kolom metode_bayar
    private const METODE_BAYAR_MAP = [
        'credit_card'   => 1,
        'bank_transfer' => 2, // Virtual Account
        'qris'          => 3,
        'gopay'         => 4,
        'shopeepay'     => 5,
        'cstore'        => 6, // Indomaret / Alfamart
    ];
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Ambil dari request biasa dulu (Trik Localhost)
            $transactionStatus = $request->transaction_status;
            $orderIdLengkap = $request->order_id;
            $paymentType = $request->payment_type ?? null;

            // Jika tidak ada di request, berarti dipanggil asli oleh Midtrans (Webhook Asli)
            if (!$transactionStatus || !$orderIdLengkap) {
                $notification = new Notification();
                $transactionStatus = $notification->transaction_status;
                $orderIdLengkap = $notification->order_id;
                $paymentType = $notification->payment_type ?? null;
            }

            // Potong string "ORDER-" untuk mendapatkan ID Pesanan aslinya (12)
            $pesananId = str_replace('ORDER-', '', $orderIdLengkap);

            // Cari data pesanan di Database
            $pesanan = Pesanan::find($pesananId);

            if (!$pesanan) {
                return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            }

            // Peta payment_type ke kode integer untuk kolom metode_bayar
            $metodeBayar = $paymentType !== null ? (self::METODE_BAYAR_MAP[$paymentType] ?? 0) : null;

            // Cek status bayar dari Midtrans
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement' || $transactionStatus == 'success') {
                // UPDATE STATUS JADI LUNAS + catat metode pembayaran
                $updateData = ['status_bayar' => 1];
                if ($metodeBayar !== null) {
                    $updateData['metode_bayar'] = $metodeBayar;
                }
                $pesanan->update($updateData);

            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // PESANAN GAGAL/KADALUARSA
                $pesanan->update(['status_bayar' => 2]); // Atau status gagal
            } elseif ($transactionStatus == 'pending') {
                // MASIH MENUNGGU TRANSFER
                $updateData = ['status_bayar' => 0];
                if ($metodeBayar !== null) {
                    $updateData['metode_bayar'] = $metodeBayar;
                }
                $pesanan->update($updateData);
            }

            return response()->json(['message' => 'Status Pesanan Berhasil Diupdate']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}