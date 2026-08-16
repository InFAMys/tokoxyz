<?php

namespace Database\Factories;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @extends Factory<Barang>
 */
class BarangFactory extends Factory
{
    protected $model = Barang::class;

    private const NAMA = [
        'Gamis', 'Tunik', 'Mukena', 'Daster', 'Koko', 'Pashmina', 'Hijab Instan',
        'Khimar', 'Rok Syar\'i', 'Bergo', 'Kaos Kaki', 'Sarung', 'Peci', 'Kemeja',
        'Jilbab', 'Kerudung', 'Baju Koko', 'Longdress', 'Celana Cingkrang', 'Abaya',
    ];

    private const ADJEKTIF = ['Polos', 'Bordir', 'Renda', 'Rayon', 'Katun', 'Premium', 'Daily', 'Elegan', 'Soft', 'Motif'];

    public function definition(): array
    {
        $kode = 'BRG-'.Str::upper(Str::random(5));
        [$foto, $thumbnail] = $this->generateImages($kode);

        return [
            'id_brand' => rand(1, 6),
            'id_kategori' => rand(1, 8),
            'kode_barang' => $kode,
            'nama_barang' => fake()->randomElement(self::ADJEKTIF).' '.fake()->randomElement(self::NAMA),
            'deskripsi' => fake()->sentence(8),
            'foto' => $foto,
            'thumbnail' => $thumbnail,
            'harga' => fake()->numberBetween(50000, 250000),
            'berat' => fake()->randomFloat(3, 0.1, 2.0),
            'stok' => fake()->numberBetween(0, 50),
            'status' => fake()->randomElement(['Ditampilkan', 'Disembunyikan']),
            'preorder' => fake()->randomElement(['Tersedia', 'Tidak Tersedia']),
            'estimasi_preorder' => fake()->boolean() ? fake()->numberBetween(3, 14) : 0,
        ];
    }

    /**
     * Draw a solid-color placeholder PNG and return its stored relative paths.
     *
     * @return array{0: string[], 1: string}
     */
    public function generateImages(string $kode): array
    {
        $dir = storage_path('app/public/barangs/'.$kode);
        $thumbDir = $dir.'/thumbnails';
        File::ensureDirectoryExists($dir);
        File::ensureDirectoryExists($thumbDir);

        $hue = fake()->numberBetween(0, 360);
        $fotos = [];

        foreach ([$dir => 'foto', $thumbDir => 'thumb'] as $folder => $label) {
            $img = imagecreatetruecolor(600, 600);
            $rgb = $this->hslToRgb($hue / 360, 0.5, 0.6);
            $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
            imagefill($img, 0, 0, $color);
            $path = $folder.'/'.$label.'.png';
            imagepng($img, $path);
            imagedestroy($img);

            if ($label === 'foto') {
                $fotos[] = 'barangs/'.$kode.'/foto.png';
            } else {
                $thumbnail = 'barangs/'.$kode.'/thumbnails/thumb.png';
            }
        }

        return [$fotos, $thumbnail];
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s === 0.0) {
            $r = $g = $b = (int) ($l * 255);
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hue2rgb($p, $q, $h + 1 / 3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1 / 3);
        }

        return [(int) ($r * 255), (int) ($g * 255), (int) ($b * 255)];
    }

    private function hue2rgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1) {
            $t -= 1;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }
}
