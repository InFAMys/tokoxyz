<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Pengaturan::upsertNilai([
            'nama_toko' => 'Toko XYZ',
            'tagline' => 'Temukan produk terbaik dengan harga terjangkau',
            'deskripsi' => 'Toko XYZ adalah toko online terpercaya yang menyediakan produk berkualitas untuk kebutuhan Anda sehari-hari. Kami berkomitmen memberikan pelayanan terbaik.',
            'alamat' => 'Jl. Contoh No. 1, Jakarta',
            'no_telp' => '081234567890',
            'email' => 'halo@tokoxyz.id',
            'jam_buka' => 'Senin - Sabtu, 08.00 - 20.00 WIB',
            'instagram' => '',
            'facebook' => '',
            'tiktok' => '',
        ]);
    }
}
