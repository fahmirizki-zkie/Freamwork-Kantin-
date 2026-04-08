<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $fillable = ['pesanan_id', 'menu_id', 'jumlah', 'harga', 'subtotal', 'catatan'];

    // satu detail pesanan punya satu pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    // satu detail pesanan punya satu menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
