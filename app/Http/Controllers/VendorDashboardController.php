<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::user()->vendor->id ?? null;

        if (!$vendorId) {
            abort(403, 'Profil vendor belum dikonfigurasi untuk akun ini.');
        }

        // 1. Ambil semua item pesanan Lunas milik vendor ini
        // Kita nge-join lewat relasi eager loading
        $pesananMasuk = DetailPesanan::with(['pesanan', 'menu'])
            ->whereHas('menu', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->whereHas('pesanan', function ($query) {
                // Dianggap angka 1 adalah status lunas
                $query->where('status_bayar', 1);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.dashboard', compact('pesananMasuk'));
    }

    public function getChartData()
    {
        $vendorId = Auth::user()->vendor->id;

        // Ambil total penjualan per tanggal selama 7 hari terakhir
        // Ingat: hanya hitung subtotal atas menu milik si vendor INI
        $chartData = DB::table('detail_pesanans')
            ->join('pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
            ->join('menus', 'menus.id', '=', 'detail_pesanans.menu_id')
            ->select(DB::raw('DATE(pesanans.created_at) as tanggal'), DB::raw('SUM(detail_pesanans.subtotal) as total_pendapatan'))
            ->where('menus.vendor_id', $vendorId)
            ->where('pesanans.status_bayar', 1) // 1 = Lunas
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->limit(7)
            ->get();

        $labels = [];
        $data = [];

        foreach ($chartData as $row) {
            $labels[] = $row->tanggal;
            $data[] = $row->total_pendapatan;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
