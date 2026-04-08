<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['user_id', 'nama_vendor']; // Tambah user_id
    // satu vendor punya banyak menus 
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }   

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
