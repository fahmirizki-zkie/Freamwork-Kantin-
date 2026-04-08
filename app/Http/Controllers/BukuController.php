<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        $kategori = Kategori::all();
        return view('buku', compact('buku', 'kategori'));
    }

    public function store()
    {
        $data = request()->validate([
            'kode' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'idkategori' => 'required|exists:kategori,idkategori',
        ]);

        Buku::create($data);

        return redirect()->route('buku.index');
    }

    public function edit($id)
    {
        $buku = Buku::with('kategori')->get();
        $kategori = Kategori::all();
        $buku_edit = Buku::findOrFail($id);
        return view('buku', compact('buku', 'kategori', 'buku_edit'));
    }

    public function update($id)
    {
        $data = request()->validate([
            'kode' => 'required|string|max:50',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'idkategori' => 'required|exists:kategori,idkategori',
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update($data);

        return redirect()->route('buku.index');
    }
    
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->back();
    }
}
