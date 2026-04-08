<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PilihKotaController extends Controller
{
    /**
     * Menampilkan halaman Pilih Kota.
     * Data dikelola sepenuhnya oleh JavaScript (tidak ada database).
     */
    public function index()
    {
        return view('kota.pilih-kota');
    }
}