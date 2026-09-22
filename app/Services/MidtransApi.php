<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransApi
{
    protected string $base;

    public function __construct()
    {
        $this->base = config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    /**
     * Create a Snap transaction and return the snap_token.
     *
     * @param  array<string, mixed>  $payload
     */
    public function createSnapToken(array $payload): string
    {
        $response = Http::withBasicAuth(strval(config('services.midtrans.server_key')), '')
            ->asJson()
            ->post($this->base.'/snap/v1/transactions', $payload);

        if ($response->failed()) {
            throw new RuntimeException('Midtrans error: '.$response->body());
        }

        $token = $response->json('token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Midtrans tidak mengembalikan token pembayaran.');
        }

        return $token;
    }

    /**
     * Verify the signature sent by the payment notification webhook.
     */
    public function verifySignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        $expected = hash(
            'sha512',
            $orderId.$statusCode.$grossAmount.strval(config('services.midtrans.server_key'))
        );

        return hash_equals($expected, $signatureKey);
    }

    public function transactionStatus(string $orderId): array
    {
        $base = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        $response = Http::withBasicAuth(strval(config('services.midtrans.server_key')), '')
            ->timeout(15)
            ->get($base."/v2/{$orderId}/status");

        return $response->json() ?? [];
    }

    /**
     * Request a direct (online) refund for a transaction.
     *
     * Direct refund supports QRIS, GoPay, ShopeePay, credit card, Akulaku, Kredivo.
     * The refund_key is idempotent — reused for the same order to avoid double refund.
     */
    public function refund(string $orderId, int|string|float $amount, string $reason = ''): array
    {
        $base = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        $response = Http::withBasicAuth(strval(config('services.midtrans.server_key')), '')
            ->asJson()
            ->timeout(15)
            ->post($base."/v2/{$orderId}/refund/online/direct", [
                'refund_key' => $orderId,
                'amount' => (int) $amount,
                'reason' => $reason,
            ]);

        $body = $response->json() ?? [];

        if ($response->failed() || (string) ($body['status_code'] ?? '') !== '200') {
            throw new RuntimeException('Gagal refund di Midtrans: '.$response->body());
        }

        return $body;
    }
}
