<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class KlikresiApi
{
    protected string $base;

    protected string $key;

    protected string $courier;

    public function __construct()
    {
        $this->base = rtrim(strval(config('services.klikresi.base_url')), '/');
        $this->key = strval(config('services.klikresi.key'));
        $this->courier = strval(config('services.klikresi.courier'));
    }

    /**
     * List provinces.
     *
     * @return array<int, array<string, mixed>>
     */
    public function provinces(): array
    {
        return Cache::remember('klikresi.provinces', now()->addDay(), function (): array {
            return $this->list('/api/provinces');
        });
    }

    /**
     * List cities (kota/kabupaten) for a province.
     *
     * @return array<int, array<string, mixed>>
     */
    public function cities(int|string $provinceId): array
    {
        return Cache::remember("klikresi.cities.{$provinceId}", now()->addDay(), function () use ($provinceId): array {
            return $this->list('/api/cities', ['province_id' => $provinceId]);
        });
    }

    /**
     * List districts (kecamatan) for a city.
     *
     * @return array<int, array<string, mixed>>
     */
    public function districts(int|string $cityId): array
    {
        return Cache::remember("klikresi.districts.{$cityId}", now()->addDay(), function () use ($cityId): array {
            return $this->list('/api/districts', ['city_id' => $cityId]);
        });
    }

    /**
     * Query shipping rates (single courier) via POST JSON body.
     *
     * @return array<int, array<string, mixed>>
     */
    public function rate(int|string $origin, int|string $destination, int $weightKg): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->key])
                ->timeout(15)
                ->asJson()
                ->post($this->base.config('services.klikresi.rate_url', '/api/rates'), [
                    'origin_id' => (string) $origin,
                    'destination_id' => (string) $destination,
                    'weight' => max(1, $weightKg),
                    'courier' => $this->courier,
                ]);
        } catch (ConnectionException) {
            throw new RuntimeException('Klikresi tidak dapat dihubungi.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Klikresi error: '.$response->body());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Klikresi response tidak valid.');
        }

        return $data['data'] ?? $data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function list(string $path, array $query = []): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->key])
                ->timeout(15)
                ->get($this->base.$path, $query);
        } catch (ConnectionException) {
            throw new RuntimeException('Klikresi tidak dapat dihubungi.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Klikresi error: '.$response->body());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Klikresi response tidak valid.');
        }

        return $data['data'] ?? $data;
    }
}
