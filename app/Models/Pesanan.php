<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = ['nama', 'total', 'metode_bayar', 'status_bayar'];

    // satu pesanan punya banyak detail pesanan
    public function detail_pesanan (){
        return $this->hasMany(DetailPesanan::class);
    }
}
