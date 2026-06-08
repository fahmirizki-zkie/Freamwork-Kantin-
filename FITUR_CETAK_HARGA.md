# Dokumentasi Fitur Cetak Label Harga

## Overview

Fitur ini memungkinkan vendor untuk mencetak label harga barang dalam format stiker A4 dengan layout grid 5 kolom × 8 baris (total 40 label per halaman).

---

## Alur Kerja

### 1. Pilih Barang (Halaman Index)

**File:** `resources/views/barang/index.blade.php`

- User membuka halaman daftar barang
- User mencentang checkbox barang yang ingin dicetak labelnya
- User klik tombol **"Cetak Label"**
- Form submit ke route `label.form` dengan method POST
- Data yang dikirim: `barang_ids[]` (array ID barang yang dipilih)

**Controller:** `BarangController@formLabel`

```php
public function formLabel(Request $request)
{
    if (!$request->barang_ids) {
        return back()->with('error', 'Pilih minimal 1 barang.');
    }

    $barangs = Barang::whereIn('id_barang', $request->barang_ids)->get();

    return view('barang.form-label', compact('barangs'));
}
```

---

### 2. Atur Posisi Cetak (Form Label)

**File:** `resources/views/barang/form-label.blade.php`

User diminta mengisi 2 input:

| Input | Keterangan | Range |
|---|---|---|
| **Posisi X** | Kolom mulai cetak (horizontal) | 1 - 5 |
| **Posisi Y** | Baris mulai cetak (vertikal) | 1 - 8 |

**Contoh:**
- X = 1, Y = 1 → Mulai dari pojok kiri atas
- X = 3, Y = 2 → Mulai dari kolom 3, baris 2 (label ke-8)
- X = 5, Y = 8 → Mulai dari pojok kanan bawah (label ke-40)

**Kenapa perlu posisi?**
Karena kertas stiker A4 biasanya sudah ada grid label yang sudah jadi. Kalau sebagian label sudah terpakai, user bisa mulai cetak dari posisi yang masih kosong.

---

### 3. Generate PDF Label

**Controller:** `BarangController@generateLabel`

```php
public function generateLabel(Request $request)
{
    $x = (int) $request->x;  // Kolom mulai
    $y = (int) $request->y;  // Baris mulai

    $barangs = Barang::whereIn('id_barang', $request->barang_ids)
                     ->get()
                     ->values();

    $pdf = Pdf::loadView('barang.preview-label', compact('barangs', 'x', 'y'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('label-harga.pdf');
}
```

**File:** `resources/views/barang/preview-label.blade.php`

---

## Layout Grid Label

### Struktur Grid

```
┌─────────────────────────────────────────────────────────┐
│  A4 Portrait (210mm × 297mm)                            │
│  Margin: 4mm (top/bottom), 5mm (left/right)            │
│                                                          │
│  ┌────┬────┬────┬────┬────┐  ← Baris 1 (Y=1)          │
│  │ 1  │ 2  │ 3  │ 4  │ 5  │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 2 (Y=2)          │
│  │ 6  │ 7  │ 8  │ 9  │ 10 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 3 (Y=3)          │
│  │ 11 │ 12 │ 13 │ 14 │ 15 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 4 (Y=4)          │
│  │ 16 │ 17 │ 18 │ 19 │ 20 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 5 (Y=5)          │
│  │ 21 │ 22 │ 23 │ 24 │ 25 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 6 (Y=6)          │
│  │ 26 │ 27 │ 28 │ 29 │ 30 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 7 (Y=7)          │
│  │ 31 │ 32 │ 33 │ 34 │ 35 │                            │
│  ├────┼────┼────┼────┼────┤  ← Baris 8 (Y=8)          │
│  │ 36 │ 37 │ 38 │ 39 │ 40 │                            │
│  └────┴────┴────┴────┴────┘                            │
│     ↑    ↑    ↑    ↑    ↑                              │
│    X=1  X=2  X=3  X=4  X=5                             │
└─────────────────────────────────────────────────────────┘
```

### Ukuran Label

| Properti | Nilai |
|---|---|
| Lebar label | 38mm |
| Tinggi label | 18mm |
| Jarak antar label (horizontal) | 2mm |
| Jarak antar label (vertikal) | 2mm |
| Total kolom | 5 |
| Total baris | 8 |
| Total label per halaman | 40 |

---

## Logika Posisi

### Perhitungan Nomor Label

```php
$startNumber = ($startRow - 1) * $cols + $startCol;
```

**Contoh:**
- X=1, Y=1 → startNumber = (1-1) × 5 + 1 = **1**
- X=3, Y=2 → startNumber = (2-1) × 5 + 3 = **8**
- X=5, Y=8 → startNumber = (8-1) × 5 + 5 = **40**

### Rendering Label

```php
@for($row = 1; $row <= $rows; $row++)
    <div class="label-row">
    @for($col = 1; $col <= $cols; $col++)
        @php
            $labelNumber = ($row - 1) * $cols + $col;
        @endphp
        
        @if($labelNumber < $startNumber)
            {{-- Label kosong (sebelum posisi mulai) --}}
            <div class="label marked"></div>
        @elseif(isset($barangs[$dataIndex]))
            {{-- Label terisi data barang --}}
            <div class="label">
                <span class="item-name">{{ $barangs[$dataIndex]->nama }}</span>
                <span class="price-line">
                    <span class="currency">Rp</span>
                    <span class="price">{{ number_format($barangs[$dataIndex]->harga, 0, ',', '.') }}</span>
                </span>
            </div>
            @php $dataIndex++; @endphp
        @else
            {{-- Label kosong (setelah data habis) --}}
            <div class="label"></div>
        @endif
    @endfor
    </div>
@endfor
```

---

## Contoh Skenario

### Skenario 1: Cetak dari Awal

**Input:**
- Barang dipilih: 3 item (Nasi Goreng, Mie Ayam, Es Teh)
- X = 1, Y = 1

**Output:**
```
┌─────────┬─────────┬─────────┬─────┬─────┐
│ Nasi    │ Mie     │ Es Teh  │     │     │
│ Goreng  │ Ayam    │         │     │     │
│ Rp15000 │ Rp12000 │ Rp5000  │     │     │
├─────────┼─────────┼─────────┼─────┼─────┤
│         │         │         │     │     │
...
```

### Skenario 2: Cetak dari Tengah

**Input:**
- Barang dipilih: 2 item (Ayam Bakar, Jus Jeruk)
- X = 3, Y = 2 (mulai dari label ke-8)

**Output:**
```
┌─────┬─────┬─────┬─────┬─────┐
│     │     │     │     │     │  ← Baris 1 kosong
├─────┼─────┼─────────┼─────────┼─────┤
│     │     │ Ayam    │ Jus     │     │  ← Baris 2, mulai kolom 3
│     │     │ Bakar   │ Jeruk   │     │
│     │     │ Rp25000 │ Rp8000  │     │
├─────┼─────┼─────────┼─────────┼─────┤
...
```

---

## Routes

```php
// Form untuk pilih posisi cetak
Route::post('/label/form', [BarangController::class, 'formLabel'])
    ->name('label.form');

// Generate PDF label
Route::post('/label/generate', [BarangController::class, 'generateLabel'])
    ->name('label.generate');
```

---

## Dependencies

- **barryvdh/laravel-dompdf**: Library untuk generate PDF dari HTML
- **DomPDF**: Engine rendering PDF

Install via:
```bash
composer require barryvdh/laravel-dompdf
```

---

## Styling (CSS)

### Grid Container

```css
.grid-container {
    width: 200mm;
    display: block;
}
```

### Label Row

```css
.label-row {
    display: table;
    width: 100%;
    table-layout: fixed;
    border-spacing: 2mm 0;  /* Jarak horizontal 2mm */
}
```

### Individual Label

```css
.label {
    display: table-cell;
    width: 38mm;
    height: 18mm;
    text-align: center;
    vertical-align: top;
    padding: 0.15cm;
}
```

### Typography

```css
.item-name {
    font-size: 10pt;
    font-weight: normal;
    color: #333;
}

.price {
    font-size: 10pt;
    font-weight: bold;
}
```

---

## Kegunaan Fitur Ini

1. **Efisiensi Kertas**: Tidak perlu buang kertas stiker yang masih ada label kosong
2. **Batch Printing**: Cetak banyak label sekaligus
3. **Konsistensi**: Format label seragam untuk semua barang
4. **Hemat Waktu**: Tidak perlu tulis label manual

---

## Catatan Penting

- Ukuran label disesuaikan dengan kertas stiker A4 standar (Tom n Jerry 108)
- Pastikan printer setting ke **A4 Portrait** dan **Actual Size** (bukan Fit to Page)
- Margin printer harus minimal untuk hasil optimal
- Test print dulu di kertas biasa sebelum cetak ke stiker
