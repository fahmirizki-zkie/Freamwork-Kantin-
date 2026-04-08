<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeekEmpat extends Controller
{
    // ----------------------------------------
    // Fungsi untuk tampilkan halaman
    // Dipanggil oleh Route::get('/week4')
    // ----------------------------------------
    public function index()
    {
        return view('week4.index');
        // 'week4.index' artinya:
        // folder: resources/views/week4/
        // file  : index.blade.php
    }

    // ----------------------------------------
    // Fungsi untuk terima data dari AJAX
    // Dipanggil oleh Route::post('/week4/ajax_submit')
    // ----------------------------------------
    public function submit(Request $req)
    {
        // Ambil nilai 'name' yang dikirim dari AJAX
        // $req->post('name') → ambil dari data POST
        $data = $req->post('name');

        // Kembalikan response dalam format JSON
        // response()->json() → fitur Laravel untuk
        // otomatis convert array PHP ke JSON object
        return response()->json([
            'status'  => 'success',      // status teks
            'code'    => 200,            // kode HTTP
            'message' => 'Data received successfully', // pesan
            'data'    => [
                'name' => $data          // data yang tadi dikirim, dikembalikan lagi
            ]
        ]);
    }
}



