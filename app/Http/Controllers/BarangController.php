<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index() {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    public function create() {
        return redirect()->route('barang.index', ['tambah' => 'true']);
    }

    public function store(Request $request) {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $prefix = now()->format('ymd');
        $last = Barang::where('id_barang', 'like', $prefix . '%')
                      ->orderBy('id_barang', 'desc')
                      ->value('id_barang');
        $seq = $last ? (intval(substr($last, 6)) + 1) : 1;
        $id_barang = $prefix . str_pad($seq, 2, '0', STR_PAD_LEFT);

        Barang::create([
            'id_barang' => $id_barang,
            'nama'      => $request->nama,
            'harga'     => $request->harga,
        ]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id) {
        $barangs = Barang::all();
        $barang_edit = Barang::findOrFail($id);
        return view('barang.index', compact('barangs', 'barang_edit'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);
        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'harga'));
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy($id) {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function formLabel(Request $request)
    {
    
    if (!$request->barang_ids) {
        return back()->with('error', 'Pilih minimal 1 barang.');
    }

    $barangs = Barang::whereIn('id_barang', $request->barang_ids)->get();

    return view('barang.form-label', compact('barangs'));
    }

    public function generateLabel(Request $request)
{
    $x = (int) $request->x;
    $y = (int) $request->y;

    $barangs = Barang::whereIn(
        'id_barang',
        $request->barang_ids
    )->get()->values();

    $pdf = Pdf::loadView('barang.preview-label', compact('barangs', 'x', 'y'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('label-harga.pdf');
}
}
