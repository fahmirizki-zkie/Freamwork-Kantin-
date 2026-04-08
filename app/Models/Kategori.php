<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'idkategori';
    protected $fillable = ['nama_kategori'];

    // Relasi: kategori memiliki banyak buku
    public function bukus()
    {
        return $this->hasMany(Buku::class, 'idkategori', 'idkategori');
    }
}
