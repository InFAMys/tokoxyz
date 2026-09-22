<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Checkout;
use App\Models\Ukuran;
use RuntimeException;
use Throwable;

class CheckoutStatusService
{
    public function __construct(
        protected KlikresiApi $klikresi,
        protected MidtransApi $midtrans,
    ) {}

    /**
     * Reconcile an order's status against Midtrans and the shipping carrier.
     */
    public function reconcile(Checkout $checkout): void
    {
        if ($checkout->status === 'pending') {
            $this->reconcileMidtrans($checkout);
        }

        $this->reconcileAutoStatuses($checkout);
        $this->reconcileShipping($checkout);
    }

    public function applyMidtransStatus(Checkout $checkout, string $transactionStatus, string $paymentType): void
    {
        $map = [
            'capture' => 'paid',
            'settlement' => 'paid',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'deny' => 'deny',
            'refund' => 'refunded',
            'partial_refund' => 'partially_refunded',
            'partially_refunded' => 'partially_refunded',
        ];

        $status = $map[$transactionStatus] ?? null;

        if ($status === null) {
            return;
        }

        if ($status === 'paid') {
            $checkout->paid_at = $checkout->paid_at ?? now();
            $checkout->payment_type = $checkout->payment_type ?? $paymentType;
        }

        $wasPaid = $checkout->status === 'paid';

        if ($checkout->status === 'pending' || $status === 'paid') {
            $checkout->status = $status;
            $checkout->save();
        }

        if ($status === 'paid' && ! $wasPaid) {
            $this->decrementStock($checkout);
        }
    }

    /**
     * Fetch Klikresi tracking for a shipped order.
     *
     * @return array{status: string, histories: array<int, array{status: string, message: string, date: string}>}|null
     */
    public function trackingFor(Checkout $checkout): ?array
    {
        if (! in_array($checkout->status, ['shipping', 'delivered', 'completed'], true) || ! $checkout->no_resi) {
            return null;
        }

        try {
            $data = $this->klikresi->tracking($checkout->no_resi);
            $histories = $data['histories'] ?? [];
        } catch (Throwable $e) {
            logger()->warning('Klikresi tracking failed for checkout '.$checkout->id_checkout.': '.$e->getMessage());

            $data = null;
            $histories = [];
        }

        if (empty($histories) && $this->isFakeResi($checkout->no_resi)) {
            try {
                $data = (new FakeKlikresiApi)->tracking($checkout->no_resi);
                $histories = $data['histories'] ?? [];
            } catch (Throwable) {
                $data = null;
                $histories = [];
            }
        }

        if (! is_array($histories) || $histories === []) {
            return null;
        }

        return [
            'status' => (string) ($data['status'] ?? ''),
            'histories' => collect($histories)
                ->filter(fn ($h) => is_array($h))
                ->sortByDesc('date')
                ->values()
                ->map(fn (array $h) => [
                    'status' => (string) ($h['status'] ?? ''),
                    'message' => (string) ($h['message'] ?? ''),
                    'date' => (string) ($h['date'] ?? ''),
                ])
                ->all(),
        ];
    }

    public function decrementStockForItems($rows): void
    {
        foreach ($rows as $item) {
            $idUkuran = $item['id_ukuran'] ?? $item->id_ukuran ?? null;
            $idBarang = $item['id_barang'] ?? $item->id_barang;
            $jumlah = (int) ($item['jumlah_barang'] ?? $item->jumlah_barang);

            if ($item['is_preorder'] ?? $item->is_preorder ?? false) {
                continue;
            }

            if ($idUkuran) {
                $ukuran = Ukuran::where('id_ukuran', $idUkuran)->first();

                if ($ukuran) {
                    $ukuran->stok_ukuran = max(0, (int) $ukuran->stok_ukuran - $jumlah);
                    $ukuran->save();
                }
            } else {
                $barang = Barang::where('id_barang', $idBarang)->first();

                if ($barang) {
                    $barang->stok = max(0, (int) $barang->stok - $jumlah);
                    $barang->save();
                }
            }
        }
    }

    public function reconcileAutoStatuses(Checkout $checkout): void
    {
        if ($checkout->status === 'pending' && $checkout->created_at->lt(now()->subHours(24))) {
            $checkout->update(['status' => 'cancelled']);
        }

        if ($checkout->status === 'paid' && $checkout->paid_at && $checkout->paid_at->lt(now()->subDays(3))) {
            $this->restockAndRefund($checkout, 'Pesanan dibatalkan otomatis, pembayaran belum dikonfirmasi 3 hari.');
        }

        if ($checkout->status === 'processed' && $checkout->updated_at->lt(now()->subDays(3))) {
            $this->restockAndRefund($checkout, 'Pesanan dibatalkan otomatis, tidak ada tanggapan selama 3 hari.');
        }
    }

    protected function restockAndRefund(Checkout $checkout, string $reason): void
    {
        try {
            if (! $this->alreadyRefunded($checkout)) {
                $this->midtrans->refund($checkout->order_id, (float) $checkout->total_amount, $reason);
            }

            if (! $this->confirmRefunded($checkout)) {
                throw new RuntimeException('Refund belum dikonfirmasi Midtrans untuk '.$checkout->order_id);
            }
        } catch (Throwable $e) {
            logger()->error('Refund failed for checkout '.$checkout->id_checkout.' ('.($checkout->payment_type ?: 'unknown').'): '.$e->getMessage());

            $checkout->update(['refund_failed_at' => now()]);

            return;
        }

        $checkout->restoreStock();
        $checkout->update(['status' => 'refunded', 'refund_failed_at' => null]);
    }

    protected function alreadyRefunded(Checkout $checkout): bool
    {
        try {
            $data = $this->midtrans->transactionStatus($checkout->order_id);
        } catch (Throwable) {
            return false;
        }

        return $this->isRefundedStatus(strval($data['transaction_status'] ?? ''));
    }

    protected function confirmRefunded(Checkout $checkout, int $maxTries = 5): bool
    {
        for ($i = 0; $i < $maxTries; $i++) {
            try {
                $data = $this->midtrans->transactionStatus($checkout->order_id);
            } catch (Throwable) {
                $data = [];
            }

            if ($this->isRefundedStatus(strval($data['transaction_status'] ?? ''))) {
                return true;
            }

            sleep(2);
        }

        return false;
    }

    protected function isRefundedStatus(string $status): bool
    {
        return in_array($status, ['refund', 'partial_refund', 'partially_refunded'], true);
    }

    public function reconcileMidtrans(Checkout $checkout): void
    {
        try {
            $data = $this->midtrans->transactionStatus($checkout->order_id);
        } catch (Throwable) {
            return;
        }

        $this->applyMidtransStatus($checkout, strval($data['transaction_status'] ?? ''), strval($data['payment_type'] ?? ''));
    }

    public function reconcileShipping(Checkout $checkout): void
    {
        if ($checkout->status === 'shipping' && $checkout->no_resi && $this->trackingIsDelivered($checkout->no_resi)) {
            $checkout->update([
                'status' => 'delivered',
                'delivered_at' => $checkout->delivered_at ?? now(),
            ]);
        }

        if ($checkout->status === 'delivered' && $checkout->delivered_at && $checkout->delivered_at->lt(now()->subDays(7))) {
            $checkout->update(['status' => 'completed']);
        }
    }

    protected function trackingIsDelivered(string $noResi): bool
    {
        try {
            return $this->hasDeliveredMarker($this->klikresi->tracking($noResi));
        } catch (Throwable) {
            return false;
        }
    }

    protected function isFakeResi(string $noResi): bool
    {
        $u = strtoupper($noResi);

        return str_contains($u, 'DEL') || str_contains($u, 'TRK') || str_contains($u, 'PIC');
    }

    /** @param mixed $value */
    protected function hasDeliveredMarker($value): bool
    {
        if (is_string($value)) {
            return str_contains(strtolower($value), 'deliver')
                || str_contains(strtolower($value), 'sampai')
                || str_contains(strtolower($value), 'terkirim');
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->hasDeliveredMarker($item)) {
                return true;
            }
        }

        return false;
    }

    protected function decrementStock(Checkout $checkout): void
    {
        $this->decrementStockForItems($checkout->items);
    }
}
