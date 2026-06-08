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
        // Buat user vendor 1
        $user1 = User::create([
            'name' => 'Admin Vendor 1',
            'email' => 'vendor1@example.com',
            'password' => Hash::make('password'),
        ]);

        Vendor::create([
            'user_id' => $user1->id,
            'nama_vendor' => 'Warung Makan Sederhana',
        ]);

        // Buat user vendor 2
        $user2 = User::create([
            'name' => 'Admin Vendor 2',
            'email' => 'vendor2@example.com',
            'password' => Hash::make('password'),
        ]);

        Vendor::create([
            'user_id' => $user2->id,
            'nama_vendor' => 'Kantin Sehat',
        ]);

        // Buat user vendor 3
        $user3 = User::create([
            'name' => 'Admin Vendor 3',
            'email' => 'vendor3@example.com',
            'password' => Hash::make('password'),
        ]);

        Vendor::create([
            'user_id' => $user3->id,
            'nama_vendor' => 'Depot Nasi Goreng',
        ]);
    }
}
