<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['vendor_id', 'nama_menu', 'harga', 'path_gambar'];

    // satu menu punya satu vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // satu menu punya banyak detail pesanan
    public function detail_pesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}
