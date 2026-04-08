<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangSementaraDtController extends Controller
{
    /**
     * Menampilkan halaman Barang Sementara versi DataTables.
     * Data dikelola sepenuhnya oleh JavaScript (tidak ada database).
     */
    public function index()
    {
        return view('barang.barang-sementara-dt');
    }
}