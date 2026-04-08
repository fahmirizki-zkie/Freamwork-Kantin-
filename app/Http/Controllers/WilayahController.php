<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    // Tampilkan halaman utama
    // Provinsi langsung diload dari DB — tidak pakai AJAX
    // karena provinsi tidak bergantung pada apapun (level 1)
    public function index()
    {
        $provinsi = DB::table('reg_provinces')
                      ->orderBy('name')
                      ->get();

        return view('wilayah.index', compact('provinsi'));
    }

    // Ambil kota berdasarkan province_id
    // Dipanggil AJAX saat provinsi dipilih
    public function getKota($province_id)
    {
        $kota = DB::table('reg_regencies')
                  ->where('province_id', $province_id)
                  ->orderBy('name')
                  ->get();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kota
        ]);
    }

    // Ambil kecamatan berdasarkan regency_id
    // Dipanggil AJAX saat kota dipilih
    public function getKecamatan($regency_id)
    {
        $kecamatan = DB::table('reg_districts')
                       ->where('regency_id', $regency_id)
                       ->orderBy('name')
                       ->get();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kecamatan
        ]);
    }

    // Ambil kelurahan berdasarkan district_id
    // Dipanggil AJAX saat kecamatan dipilih
    public function getKelurahan($district_id)
    {
        $kelurahan = DB::table('reg_villages')
                       ->where('district_id', $district_id)
                       ->orderBy('name')
                       ->get();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $kelurahan
        ]);
    }
}
