<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangSementaraController extends Controller
{
    /**
     * Menampilkan halaman Barang Sementara.
     * Data dikelola sepenuhnya oleh JavaScript (tidak ada database).
     */
    public function index()
    {
        return view('barang.barang-sementara');
    }
}