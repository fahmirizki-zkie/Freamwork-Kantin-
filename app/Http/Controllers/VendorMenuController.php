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
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pathGambar = null;
        if ($request->hasFile('path_gambar')) {
            $destinationPath = public_path('uploads/menu');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $file = $request->file('path_gambar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $pathGambar = 'uploads/menu/' . $fileName;
        }

        Menu::create([
            'vendor_id' => $vendorId,
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'path_gambar' => $pathGambar,
        ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
        ];

        if ($request->hasFile('path_gambar')) {
            $destinationPath = public_path('uploads/menu');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $file = $request->file('path_gambar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $data['path_gambar'] = 'uploads/menu/' . $fileName;
        }

        $menu->update($data);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu diperbarui!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->path_gambar && file_exists(public_path($menu->path_gambar))) {
            unlink(public_path($menu->path_gambar));
        }

        $menu->delete();
        return redirect()->route('vendor.menu.index')->with('success', 'Menu dihapus!');
    }
}
