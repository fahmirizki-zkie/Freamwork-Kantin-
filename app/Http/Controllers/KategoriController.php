<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::with('bukus')->get();
        return view('kategori', compact('kategori'));
    }

    public function store()
    {
        $data = request()->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create($data);

        return redirect()->route('kategori.index');
    }

    public function edit($id)
    {
        $kategori = Kategori::with('bukus')->get();
        $kategori_edit = Kategori::findOrFail($id);
        return view('kategori', compact('kategori', 'kategori_edit'));
    }

    public function update($id)
    {
        $data = request()->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update($data);

        return redirect()->route('kategori.index');
    }
    
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back();
    }
}
