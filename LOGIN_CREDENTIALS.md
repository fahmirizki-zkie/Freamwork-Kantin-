# Login Credentials - Sistem Kantin

## Akses Vendor Dashboard

**URL:** http://vendor.localhost:8000/login

### Default Login (Setelah Seeder)

**Email:** vendor@example.com  
**Password:** password

---

## Cara Membuat User Vendor Baru

### 1. Via Tinker (Manual)

```bash
php artisan tinker
```

```php
// Buat user
$user = \App\Models\User::create([
    'name' => 'Nama Vendor',
    'email' => 'vendor@example.com',
    'password' => bcrypt('password')
]);

// Buat vendor dan hubungkan dengan user
\App\Models\Vendor::create([
    'user_id' => $user->id,
    'nama_vendor' => 'Warung Makan Sederhana'
]);
```

### 2. Via Seeder (Otomatis)

Buat file `database/seeders/VendorSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Admin Vendor',
            'email' => 'vendor@example.com',
            'password' => Hash::make('password'),
        ]);

        Vendor::create([
            'user_id' => $user->id,
            'nama_vendor' => 'Warung Makan Sederhana',
        ]);
    }
}
```

Jalankan:
```bash
php artisan db:seed --class=VendorSeeder
```

---

## Akses Customer (Pemesanan)

**URL:** http://localhost:8000

Tidak perlu login, langsung bisa pesan.

---

## Catatan Penting

- User harus punya relasi ke tabel `vendors` (kolom `user_id`)
- Kalau user login tapi tidak punya vendor, akan muncul error 403
- Password default: `password` (bisa diganti sesuai kebutuhan)
