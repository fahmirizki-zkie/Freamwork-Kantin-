# Tutorial: Membangun Fitur Master Menu & Dashboard Vendor

Dokumen ini berisi panduan rute lengkap (beserta kodenya) untuk membangun fitur *Master Menu*, melihat *Pesanan Lunas*, dan menampilkan grafik *Chart.js* pada **Domain Vendor**. 

Silakan ikuti instruksi secara runut dari Step 1 hingga Step 6!

---

## Step 1: Menghubungkan Tabel Users & Vendors

Vendor harus bisa *login*, sehingga data Menu yang ditampilkan adalah milik vendor tersebut saja. Kita butuh kolom `user_id` di tabel `vendors`.

1. **Buat file migration baru** via terminal:
   ```bash
   php artisan make:migration add_user_id_to_vendors_table --table=vendors
   ```
2. Isi file migration barunya:
   ```php
   public function up(): void
   {
       Schema::table('vendors', function (Blueprint $table) {
           $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
       });
   }

   public function down(): void
   {
       Schema::table('vendors', function (Blueprint $table) {
           $table->dropForeign(['user_id']);
           $table->dropColumn('user_id');
       });
   }
   ```
3. **Eksekusi migration** di terminal:
   ```bash
   php artisan migrate
   ```

4. Ubah model `app/Models/Vendor.php` agar bisa menyimpan `user_id`:
   ```php
   protected $fillable = ['user_id', 'nama_vendor']; // Tambah user_id

   public function user()
   {
       return $this->belongsTo(User::class);
   }
   ```

5. Ubah juga model `app/Models/User.php` untuk merujuk ke Vendor. Taruh fungsi ini di paling bawah (sebelum penutup `}`):
   ```php
   // Satu User hanya punya satu Vendor (One to One)
   public function vendor()
   {
       return $this->hasOne(Vendor::class);
   }
   ```

---

## Step 2: Mengatur Routing (Jalur Web)

Buka `routes/web.php`. Cari bagian `Route::domain('vendor.localhost')`, lalu fokus pada grup **Protected Routes**. Ubah menjadi seperti ini:

```php
use App\Http\Controllers\VendorMenuController; // Tambahkan import ini di paling atas!

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard Vendor
    Route::get('/', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
    
    // 2. Data AJAX untuk Chart di Dashboard
    Route::get('/chart-data', [VendorDashboardController::class, 'getChartData'])->name('vendor.chart');

    // 3. Modul Master Menu (Mencakup tampil list, simpan, update, destroy)
    Route::resource('/menu', VendorMenuController::class)->names('vendor.menu');
});
```

---

## Step 3: Controller Master Menu

Jalankan perintah ini di terminal untuk membuat Controller:
```bash
php artisan make:controller VendorMenuController --resource
```

Setelah terbuat, buka `app/Http/Controllers/VendorMenuController.php` dan timpakan kode ini. *Pastikan fungsi Create/Edit tidak diperlukan karena kita pakai Modal nanti.*

```php
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
```

---

## Step 4: Controller Dashboard & Data Chart

Buka `app/Http/Controllers/VendorDashboardController.php`. Jika belum ada, buat dengan manual: `php artisan make:controller VendorDashboardController`.

Logikanya: Karena satu pesanan bisa diakses lintas menu, kita harus memfilter *Detail Pesanan* yang merujuk pada *Menu milik Vendor Login*, dan hanya pesanan yang statusnya = *1 (Lunas)*.

Isi kodenya dengan ini:

```php
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
```

---

## Step 5: View Master Menu

Supaya rapi, buat folder baru `resources/views/vendor/menu/` dan buat file `index.blade.php` di dalamnya.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Menu - Vendor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('vendor.dashboard') }}">🔙 Ke Dashboard</a>
        <span class="navbar-text text-white">Master Menu Toko</span>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Menu Makanan</h5>
            <!-- Tombol Pemicu Modal Tambah Menu -->
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                + Tambah Menu
            </button>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $index => $menu)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $menu->nama_menu }}</td>
                            <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                            <td>
                                <!-- Tombol Hapus (Minimalis form) -->
                                <form action="{{ route('vendor.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Anda belum memiliki menu!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('vendor.menu.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tambah Menu Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label>Nama Menu</label>
                  <input type="text" name="nama_menu" class="form-control" required>
              </div>
              <div class="mb-3">
                  <label>Harga (Rp)</label>
                  <input type="number" name="harga" class="form-control" required>
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Menu</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## Step 6: View Dashboard & Chart Penjualan

Buat file baru di `resources/views/vendor/dashboard.blade.php`.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand" href="#">📦 Dashboard Vendor</a>
        <div>
            <a href="{{ route('vendor.menu.index') }}" class="btn btn-outline-light btn-sm me-2">Daftar Menu</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        
        <!-- KOLOM STATISTIK (CHART) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📈 Grafik Penjualan (7 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <!-- Canvas Chart.js -->
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
        </div>

        <!-- KOLOM PESANAN LUNAS -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✔️ Item Dipesan (Sudah Lunas)</h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pembeli</th>
                                <th>Menu</th>
                                <th>Qty</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesananMasuk as $item)
                                <tr>
                                    <td>{{ $item->pesanan->nama }} <br> <small class="text-muted">#{{ $item->pesanan->id }}</small></td>
                                    <td>{{ $item->menu->nama_menu }}</td>
                                    <td>{{ $item->jumlah }}x</td>
                                    <td class="text-success fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada pesanan lunas untuk Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Memanggil API Grafik lewat AJAX native (Fetch API)
    fetch("{{ route('vendor.chart') }}")
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('penjualanChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Coba ubah 'bar' menjadi 'line' kalau mau tipe garis!
                data: {
                    labels: data.labels, // Tanggal (Sumbu X)
                    datasets: [{
                        label: 'Total Pendapatan (Rp)',
                        data: data.data, // Pemasukan (Sumbu Y)
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
</script>

</body>
</html>
```

---

> [!CAUTION]
> **Catatan Penting:** 
> Pastikan data status pembayaran dan logika otentikasi login Anda berfungsi. Tutorial ini mengasumsikan Anda sudah memiliki user login yang terhubung ke satu Vendor melalui `user_id`.
