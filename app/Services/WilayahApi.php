<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WilayahApi
{
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(strval(config('services.wilayah.base_url', 'https://wilayah.id/api')), '/');
    }

    /**
     * List provinces.
     *
     * @return array<int, array<string, mixed>>
     */
    public function provinces(): array
    {
        return Cache::remember('wilayah.provinces', now()->addDay(), function (): array {
            return $this->get('/provinces.json');
        });
    }

    /**
     * List regencies (kota/kabupaten) for a province.
     *
     * @return array<int, array<string, mixed>>
     */
    public function regencies(int|string $provinceId): array
    {
        return Cache::remember("wilayah.regencies.{$provinceId}", now()->addDay(), function () use ($provinceId): array {
            return $this->get("/regencies/{$provinceId}.json");
        });
    }

    /**
     * List districts (kecamatan) for a regency.
     *
     * @return array<int, array<string, mixed>>
     */
    public function districts(int|string $regencyId): array
    {
        return Cache::remember("wilayah.districts.{$regencyId}", now()->addDay(), function () use ($regencyId): array {
            return $this->get("/districts/{$regencyId}.json");
        });
    }

    /**
     * List villages (kelurahan/desa) for a district.
     *
     * @return array<int, array<string, mixed>>
     */
    public function villages(int|string $districtId): array
    {
        return Cache::remember("wilayah.villages.{$districtId}", now()->addDay(), function () use ($districtId): array {
            return $this->get("/villages/{$districtId}.json");
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function get(string $path): array
    {
        try {
            $response = Http::timeout(15)->get($this->base.$path);
        } catch (ConnectionException) {
            throw new RuntimeException('Wilayah tidak dapat dihubungi.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Wilayah error: '.$response->body());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Wilayah response tidak valid.');
        }

        return $data['data'] ?? $data;
    }
}
