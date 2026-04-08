<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // Tampilkan halaman POS
    public function index()
    {
        return view('pos.index');
    }

    // Cari barang berdasarkan kode
    // Dipanggil AJAX saat kasir tekan Enter di input kode
    public function cariBarang($kode)
    {
        $barang = DB::table('barang')
                    ->where('id_barang', $kode)
                    ->first(); // ambil 1 data saja

        // Kalau barang tidak ditemukan
        if (!$barang) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Barang tidak ditemukan'
            ]);
        }

        // Kalau barang ditemukan
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Barang ditemukan',
            'data'    => $barang
            // data berisi: id_barang, nama, harga, timestamp
        ]);
    }

    // Simpan transaksi ke DB
    // Dipanggil AJAX saat kasir klik "Bayar"
    public function bayar(Request $req)
    {
        // Ambil data items dari request
        // items = array of { id_barang, nama, harga, jumlah, subtotal }
        $items = $req->post('items');
        $total = $req->post('total');

        // Simpan ke tabel penjualan (header)
        $id_penjualan = DB::table('penjualan')->insertGetId([
            'total'     => $total,
            'timestamp' => now()
        ]);

        // Simpan tiap item ke tabel penjualan_detail
        foreach ($items as $item) {
            DB::table('penjualan_detail')->insert([
                'id_penjualan' => $id_penjualan,
                'id_barang'    => $item['id_barang'],
                'jumlah'       => $item['jumlah'],
                'subtotal'     => $item['subtotal']
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Transaksi berhasil disimpan',
            'data'    => ['id_penjualan' => $id_penjualan]
        ]);
    }
}