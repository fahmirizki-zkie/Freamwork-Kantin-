<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class VendorMenuController extends Controller
{
    public function index()
    {
        // Ambil ID Vendor dari user yang sedang login
        $vendorId = Auth::user()->vendor->id ?? null;
        
        if (!$vendorId) {
            abort(403, 'Akses Ditolak. Hubungi Admin karena Anda tidak punya toko Vendor');
        }

        // Ambil menu milik vendor tersebut
        $menus = Menu::where('vendor_id', $vendorId)->orderBy('nama_menu')->get();
        return view('vendor.menu.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $vendorId = Auth::user()->vendor->id;

        // Validasi input
        $request->validate([
            'nama_menu' => 'required',
            'harga' => 'required|numeric'
        ]);

        Menu::create([
            'vendor_id' => $vendorId,
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'path_gambar' => 'default.jpg' // Default sementara statis
        ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update([
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga
        ]);
        return redirect()->route('vendor.menu.index')->with('success', 'Menu diperbarui!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return redirect()->route('vendor.menu.index')->with('success', 'Menu dihapus!');
    }
}
