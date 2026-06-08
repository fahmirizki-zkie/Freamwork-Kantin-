<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_barang',
        'nama_barang',
        'nama',
        'harga',
    ];

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_barang'] ?? null;
    }

    public function setNamaAttribute($value): void
    {
        $this->attributes['nama_barang'] = $value;
    }
}
