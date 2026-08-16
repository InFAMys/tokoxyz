<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Kategori;
use App\Models\Ukuran;
use Database\Factories\BarangFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Elzatta', 'Al Bunayya', 'Zoya', 'Ria Miranda', 'Rabbani', 'Khadijah',
        ];
        $kategoris = [
            'Gamis', 'Hijab', 'Mukena', 'Baju Koko', 'Tunik', 'Daster', 'Sarung', 'Peci/Kopiah',
        ];

        foreach ($brands as $nama) {
            Brand::create([
                'nama_brand' => $nama,
                'logo' => $this->makeBrandLogo($nama),
            ]);
        }

        foreach ($kategoris as $nama) {
            Kategori::create(['nama_kategori' => $nama]);
        }

        $factory = new BarangFactory;

        for ($i = 0; $i < 30; $i++) {
            $barang = $factory->create([
                'id_brand' => rand(1, count($brands)),
                'id_kategori' => rand(1, count($kategoris)),
            ]);

            if (fake()->boolean(60)) {
                $sizes = fake()->randomElements(['S', 'M', 'L', 'XL', 'XXL'], rand(2, 4));

                foreach ($sizes as $ukuran) {
                    Ukuran::create([
                        'id_barang' => $barang->id_barang,
                        'nama_ukuran' => $ukuran,
                        'ukuran' => $ukuran,
                        'harga_ukuran' => fake()->boolean() ? fake()->numberBetween(45000, 250000) : null,
                        'stok_ukuran' => fake()->numberBetween(0, 20),
                    ]);
                }
            }
        }
    }

    private function makeBrandLogo(string $nama): string
    {
        $path = storage_path('app/public/brands/'.Str::slug($nama).'.png');
        $img = imagecreatetruecolor(200, 200);
        $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($img, 0, 0, $color);
        imagepng($img, $path);
        imagedestroy($img);

        return 'brands/'.Str::slug($nama).'.png';
    }
}
