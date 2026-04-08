# Panduan Lengkap: Integrasi Payment Gateway (Virtual Account & QRIS)

Untuk fitur pembayaran otomatis (Virtual Account / QRIS) di Indonesia, yang paling umum, gratis untuk dicoba (environment Sandbox/Testing), dan mudah digunakan adalah **Midtrans**.

Berikut adalah panduan lengkap step-by-step dari instalasi sampai pesanan berhasil lunas secara sistem.

---

## Step 1: Buat Akun Midtrans (Sandbox)

1. Buka website [Midtrans](https://midtrans.com/) dan daftar akun.
2. Setelah login, pastikan kamu berada di mode **Sandbox** (Testing).
3. Buka menu **Settings > Access Keys**.
4. Di sana kamu akan melihat **Client Key** dan **Server Key**. Catat kedua _key_ ini.

---

## Step 2: Konfigurasi di `.env`

Buka file `.env` di project Laravel-mu, lalu tambahkan baris berikut di paling bawah file:

```env
MIDTRANS_MERCHANT_ID=Isi_Dengan_Merchant_ID_Kamu
MIDTRANS_CLIENT_KEY=Isi_Dengan_Client_Key_Kamu
MIDTRANS_SERVER_KEY=Isi_Dengan_Server_Key_Kamu
MIDTRANS_IS_PRODUCTION=false
```

_(Ganti kode-kode di atas dengan yang kamu dapatkan dari dashboard Midtrans)_

---

## Step 3: Install Package Midtrans PHP

Agar Laravel kita bisa ngobrol dengan Midtrans gampang, buka terminal Laragon/VSCode dan jalankan perintah ini:

```bash
composer require midtrans/midtrans-php
```

---

## Step 4: Konfigurasi & Modifikasi `CustomerController`

Buka `app/Http/Controllers/CustomerController.php`. Kita perlu mengupdate fungsi `bayar` agar tidak sekadar menyimpan pesanan, melainkan juga **Me-Request ke Midtrans** untuk mendapatkan _Token Pembayaran (Snap Token)_.

_Timpa atau sesuaikan Controller milikmu dengan kode ini:_

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Midtrans\Config;
use Midtrans\Snap;

class CustomerController extends Controller
{
    // ... [Fungsi index dan getMenu milikmu tetap biarkan sama] ...

    public function bayar(Request $request)
    {
        // 1. Simpan data Pesanan (Misal tabel pesanans)
        $pesanan = Pesanan::create([
            'nama' => $request->nama_pembeli,
            'no_meja' => $request->no_meja,
            'status_bayar' => 0, // 0 = Belum Lunas
            'total_harga' => $request->total_harga // Total belanja
        ]);

        // 2. Simpan Detail Pesanan (Keranjang)
        foreach ($request->keranjang as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['menu_id'],
                'jumlah' => $item['jumlah'],
                'subtotal' => $item['subtotal']
            ]);
        }

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
                'gross_amount' => $pesanan->total_harga,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
                // Bisa tambah email/nomor telepon jika mau struk ke email customer
            ],
            // Jika mau menampilkan spesifik QRIS dan VA saja
            'enabled_payments' => [
                'qris', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va'
            ]
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
}
```

---

## Step 5: Tampilkan Pop-Up Pembayaran di View (Frontend)

Di tampilan customer (tempat tombol Checkout / Bayar ditekan), kamu perlu menambahkan **Script Midtrans** agar _Snap Token_ dari controller memunculkan pop-up pembayaran QRIS & VA.

Buka file _blade customer_ milikmu (misal `index.blade.php`), tambahkan kode ini tepat sebelum penutup tag `</body>`:

```html
<!-- Script Sandbox Midtrans -->
<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"
></script>
```

Lalu di bagian JavaScript yang menghandle klik tombol **"Bayar"**, ubah menjadi seperti ini:

```javascript
function checkout(dataKeranjang) {
    // Asumsi keranjang & form sudah disiapkan
    fetch("/bayar", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify(dataKeranjang),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                // JIKA SUKSES, PANGGIL POP-UP MIDTRANS PAKAI TOKEN-NYA
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        alert("Pembayaran Berhasil! Pesanan diproses.");
                        window.location.reload(); // Refresh layar setelah lunas
                    },
                    onPending: function (result) {
                        alert("Menunggu pembayaran Anda...");
                    },
                    onError: function (result) {
                        alert("Pembayaran gagal, silakan coba lagi.");
                    },
                    onClose: function () {
                        alert(
                            "Anda menutup pop-up sebelum menyelesaikan pembayaran.",
                        );
                    },
                });
            }
        });
}
```

**Penjelasan Singkat:** Ketika `CustomerController` mengirim balik JSON yang berisi `snap_token`, fungsi `window.snap.pay()` akan mengeksekusi pop-up cantik dari Midtrans langsung di layarmu.

---

## Step 6: Callback / Webhook Midtrans (Mengubah Status Menjadi "Lunas" Otomatis)

Ketika customer selesai transfer Virtual Account (walaupun dia sudah menutup website kita), server Midtrans akan diam-diam "mengetuk" pintu server kita dari jalur belakang (lewat URL Webhook) untuk memberitahukan _"Eh, si A udah bayar loh!"_. Disitulah fitur Auto-Update Lunas bekerja.

**A. Buat Route Khusus Webhook:**
Buka file `routes/api.php` _(Bukan web.php karena ini request dari server luar yg tidak butuh token CSRF)_, lalu tambahkan rute ini:

```php
use App\Http\Controllers\PaymentController;

Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback']);
```

**B. Buat Controller Webhook:**
Di terminal, jalankan:

```bash
php artisan make:controller PaymentController
```

Lalu buka `app/Http/Controllers/PaymentController.php`, isi dengan kode ini:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderIdLengkap = $notification->order_id; // Hasilnya: "ORDER-12"

            // Potong string "ORDER-" untuk mendapatkan ID Pesanan aslinya (12)
            $pesananId = str_replace('ORDER-', '', $orderIdLengkap);

            // Cari data pesanan di Database
            $pesanan = Pesanan::find($pesananId);

            if (!$pesanan) {
                return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            }

            // Cek status bayar dari Midtrans
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement' || $transactionStatus == 'success') {
                // UPDATE STATUS JADI LUNAS
                $pesanan->update(['status_bayar' => 1]);

            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // PESANAN GAGAL/KADALUARSA
                $pesanan->update(['status_bayar' => 2]); // Atau status gagal
            } elseif ($transactionStatus == 'pending') {
                // MASIH MENUNGGU TRANSFER
                $pesanan->update(['status_bayar' => 0]);
            }

            return response()->json(['message' => 'Status Pesanan Berhasil Diupdate']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
```

> **CATATAN PENTING WEBHOCK:**
> Karena komputer lokalmu (localhost) tidak bisa diakses dari internet luar, Midtrans tidak bisa mengirim Webhook/Callback ini ke laptopmu secara langsung saat testing.
>
> Pengujian Webhook ini (Status Lunas) idealnya baru bekerja maksimal setelah websitemu di-_hosting_ (online) atau menggunakan alat _Tunneling_ seperti **Ngrok**.

---

### Selesai! 🎉

Dengan begitu, 2 poin tugasmu telah tercapai:

1. Muncul Pop-up bayar QRIS / VA sesaat setelah pesan.
2. Ketika dibayar, status pesanan di dalam database berubah jadi _Lunas (1)_.
