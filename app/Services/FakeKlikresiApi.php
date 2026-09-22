<?php

namespace App\Services;

/**
 * Fake Klikresi tracking used for testing/offline development.
 *
 * Only overrides tracking(); everything else (rate/ongkir) stays real.
 * Status is driven by a keyword in the resi number (case-insensitive):
 *  - DEL => delivered (auto-advances order to "delivered")
 *  - TRK => in transit
 *  - PIC => picked up
 *  - any other resi delegates to the real Klikresi API (valid or invalid)
 */
class FakeKlikresiApi extends KlikresiApi
{
    public function tracking(string $trackingNumber): array
    {
        $u = strtoupper($trackingNumber);
        $iso = fn ($date) => $date->format('Y-m-d\TH:i:sP');
        $at = fn (int $daysAgo, int $hours) => $iso(now()->subDays($daysAgo)->setTime(0, 0)->addHours($hours));

        $base = [
            ['status' => 'InTransit', 'message' => 'Item has been picked and ready to be shipped.', 'date' => $at(3, 10)],
            ['status' => 'InTransit', 'message' => 'Manifes.', 'date' => $at(3, 14)],
            ['status' => 'InTransit', 'message' => 'Paket akan dikirimkan ke PTI_GATEWAY.', 'date' => $at(2, 9)],
            ['status' => 'InTransit', 'message' => 'Paket tiba di PTI_GATEWAY. Mobil.', 'date' => $at(2, 15)],
            ['status' => 'InTransit', 'message' => 'Paket akan dikirimkan ke SUB_GATEWAY. Mobil.', 'date' => $at(2, 20)],
            ['status' => 'InTransit', 'message' => 'Paket tiba di SUB_GATEWAY.', 'date' => $at(1, 8)],
            ['status' => 'InTransit', 'message' => 'Paket akan dikirimkan ke MENGANTI. Mobil.', 'date' => $at(1, 13)],
            ['status' => 'InTransit', 'message' => 'Paket tiba di MENGANTI. Mobil.', 'date' => $at(1, 17)],
        ];

        if (str_contains($u, 'DEL')) {
            return [
                'status' => 'Delivered',
                'histories' => array_merge($base, [
                    ['status' => 'InTransit', 'message' => 'Paket akan dikirim ke alamat penerima.', 'date' => $at(0, 8)],
                    ['status' => 'InTransit', 'message' => 'Paket akan dikirimkan ke alamat penerima. Mobil.', 'date' => $at(0, 10)],
                    ['status' => 'Delivered', 'message' => 'Item has been delivered.', 'date' => $at(0, 12)],
                ]),
            ];
        }

        if (str_contains($u, 'TRK')) {
            return [
                'status' => 'InTransit',
                'histories' => array_merge($base, [
                    ['status' => 'InTransit', 'message' => 'Paket akan dikirim ke alamat penerima.', 'date' => $at(0, 8)],
                    ['status' => 'InTransit', 'message' => 'Paket sedang dalam perjalanan menuju tujuan.', 'date' => $at(0, 11)],
                ]),
            ];
        }

        if (str_contains($u, 'PIC')) {
            return [
                'status' => 'InTransit',
                'histories' => array_slice($base, 0, 3),
            ];
        }

        return parent::tracking($trackingNumber);
    }
}
