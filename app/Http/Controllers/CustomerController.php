<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Midtrans\Config;
use Midtrans\Snap;

class CustomerController extends Controller
{
    // Tampilkan halaman utama customer
    // Konsep sama seperti WilayahController->index
    // Load vendor langsung dari DB (level 1, tidak pakai AJAX)
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('nama_vendor')->get();

        // Fitur: Ambil riwayat order pelanggan dari Session Browser mereka
        $myOrderIds = $request->session()->get('my_orders', []);
        
        // Load data Pesanan beserta detail dan menu-nya
        $myOrders = Pesanan::with(['detail_pesanan.menu.vendor'])
                    ->whereIn('id', $myOrderIds)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('customer.index', compact('vendors', 'myOrders'));
    }


    // AJAX: Ambil menu berdasarkan vendor_id
    // Konsep sama seperti WilayahController->getKota()
    // Dipanggil saat customer pilih vendor di dropdown
    public function getMenu($vendor_id)
    {
        $menus = Menu::where ('vendor_id', $vendor_id)
                ->orderBy('nama_menu')
                ->get();

        return response()->json([
            'status' => 'success',
            'data' => $menus,
        ]);
    }

    // AJAX: Simpan pesanan ke database
    // Konsep sama seperti PosController->bayar()
    // Dipanggil saat customer klik tombol "Bayar"
    public function bayar(Request $request)
    {
        // 1. Hitung nomor guest otomatis
        $lastPesanan = Pesanan::orderBy('id','desc')->first();
        $nextNumber =  $lastPesanan ?  ($lastPesanan->id + 1) : 1;
        $guestName = 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);


        // 2. Simpan ke tabel pesanans (header)
        $pesanan = Pesanan::create([
            'nama'         => $guestName,
            'total'        => $request->input('total'),
            'status_bayar' => 0,
        ]);

        // 3. Simpan ke tabel pesanans (header)
        foreach ($request->items as $items){
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id'    => $items['menu_id'],
                'jumlah'     => $items['jumlah'],
                'harga'      => $items['harga'],
                'subtotal'   => $items['subtotal'],
                'catatan'    => $items['catatan']?? NULL,
            ]);
        }

        // Simpan ID pesanan yang baru ke sesi browser pelanggan
        $request->session()->push('my_orders', $pesanan->id);
   
        // ==========================================
        // 3. KONFIGURASI MIDTRANS
        // ==========================================
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true; // Untuk kartu kredit

        // 4. Siapkan parameter yang diminta Midtrans
        $params = [
            'transaction_details' => [
                // Order ID Midtrans harus UNIK. Kita gabungkan tulisan ORDER- dengan ID Pesanan
                'order_id' => 'ORDER-' . $pesanan->id,
                'gross_amount' => $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
                // Bisa tambah email/nomor telepon jika mau struk ke email customer
            ],
            // Aktifkan semua metode pembayaran umum: QRIS, VA, e-wallet, kartu kredit, dll.
            'enabled_payments' => [
                'credit_card',
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
                'gopay', 'shopeepay', 'qris',
                'indomaret', 'alfamart',
            ],
        ];

        // 5. Minta Token Snap ke Midtrans
        try {
            $snapToken = Snap::getSnapToken($params);

            // Kembalikan respons dalam bentuk JSON (karena frontend memanggil pakai AJAX/Fetch)
            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'pesanan_id' => $pesanan->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    
    }

    // AJAX: Cek status terkini sebuah pesanan berdasarkan ID
    public function cekStatusPesanan($id)
    {
        $pesanan = Pesanan::find($id);

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'status'      => 'success',
            'status_bayar' => $pesanan->status_bayar,
        ]);
    }

    // AJAX: Hapus riwayat pesanan dari session browser (HANYA DARI FRONTEND/SESSION)
    public function hapusRiwayat(Request $request)
    {
        // Hapus dari session browser pelanggan, sehingga tidak muncul lagi di modal "Pesanan Saya"
        $request->session()->forget('my_orders');
        $request->session()->save(); // Paksa simpan perubahan session langsung
        
        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat pesanan berhasil dihapus dari perangkat ini'
        ]);
    }
}