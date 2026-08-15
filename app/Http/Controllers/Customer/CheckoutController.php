<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Barang;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\Customer;
use App\Models\Diskon;
use App\Models\Keranjang;
use App\Models\Ukuran;
use App\Services\KlikresiApi;
use App\Services\MidtransApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        protected KlikresiApi $klikresi,
        protected MidtransApi $midtrans,
    ) {}

    public function create(): View|RedirectResponse
    {
        $cart = $this->cart();
        $customer = $this->customer();

        if ($cart->isEmpty()) {
            return redirect()->route('keranjang.index');
        }

        $items = $this->cartItems($cart);
        $subtotal = $this->sum($items, 'subtotal');
        $beratTotal = $this->sum($items, 'berat');
        $alamats = $customer->alamats()->get();

        return view('customer.checkout.create', compact('items', 'subtotal', 'beratTotal', 'alamats'));
    }

    public function rate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_alamat' => ['required', 'integer'],
        ]);

        $alamat = $this->ownedAlamat((int) $data['id_alamat']);

        if (! $alamat->id_kecamatan || ! config('services.klikresi.origin_id')) {
            return response()->json(['message' => 'Alamat belum memiliki kecamatan tujuan.'], 422);
        }

        $beratKg = $this->cartWeightKg();

        try {
            $options = $this->normalizeRates(
                $this->klikresi->rate(config('services.klikresi.origin_id'), $alamat->id_kecamatan, $beratKg)
            );
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        if ($options === []) {
            return response()->json(['message' => 'Tidak ada ongkir tersedia.'], 404);
        }

        return response()->json(['options' => $options]);
    }

    public function diskon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode_diskon' => ['required', 'string', 'max:10'],
        ]);

        $subtotal = $this->sum($this->cartItems($this->cart()), 'subtotal');

        try {
            [$kode, $nominal] = $this->verifyDiscount($subtotal, $data['kode_diskon']);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['code' => $kode, 'nominal' => $nominal]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_alamat' => ['required', 'integer'],
            'shipping_service' => ['required', 'string'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'kode_diskon' => ['nullable', 'string', 'max:10'],
        ], [
            'id_alamat.required' => 'Pilih Alamat Pengiriman!',
            'shipping_service.required' => 'Pilih Layanan Pengiriman!',
            'shipping_cost.required' => 'Ongkir tidak valid.',
        ]);

        $alamat = $this->ownedAlamat((int) $data['id_alamat']);
        $cart = $this->cart();

        if ($cart->isEmpty()) {
            return redirect()->route('keranjang.index');
        }

        $items = $this->cartItems($cart);
        $subtotal = $this->sum($items, 'subtotal');
        $beratTotal = $this->sum($items, 'berat');

        // Re-query shipping cost server-side — never trust the client value.
        $shippingCost = $this->verifyShippingCost($alamat, (float) $data['shipping_cost'], $data['shipping_service']);

        // Discount (percentage off subtotal, server-side).
        [$kodeDiskon, $diskonNominal] = $this->verifyDiscount($subtotal, $data['kode_diskon'] ?? null);

        $totalAmount = max(0, round($subtotal - $diskonNominal + $shippingCost, 2));

        $customer = $this->customer();

        try {
            $checkout = DB::transaction(function () use (
                $customer, $alamat, $items, $subtotal, $beratTotal,
                $shippingCost, $data, $kodeDiskon, $diskonNominal, $totalAmount
            ) {
                $checkout = Checkout::create([
                    'id_cst' => $customer->id_cst,
                    'id_alamat' => $alamat->id_alamat,
                    'order_id' => $this->orderId(),
                    'customer_name' => $customer->nama,
                    'customer_email' => $customer->email,
                    'customer_telp' => $customer->no_telp,
                    'subtotal' => $subtotal,
                    'diskon_nominal' => $diskonNominal,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'berat_total' => $beratTotal,
                    'shipping_courier' => 'J&T',
                    'shipping_service' => $data['shipping_service'],
                    'shipping_address' => $this->addressLabel($alamat),
                    'kode_diskon' => $kodeDiskon,
                    'status' => $totalAmount > 0 ? 'pending' : 'paid',
                    'paid_at' => $totalAmount > 0 ? null : now(),
                ]);

                foreach ($items as $item) {
                    CheckoutItem::create([
                        'id_checkout' => $checkout->id_checkout,
                        'id_barang' => $item['barang']->id_barang,
                        'id_ukuran' => $item['ukuran']->id_ukuran ?? null,
                        'nama_barang' => $item['barang']->nama_barang,
                        'ukuran_name' => $item['ukuran_name'],
                        'unit_price' => $item['unit_price'],
                        'jumlah_barang' => $item['jumlah_barang'],
                        'subtotal' => $item['subtotal'],
                        'berat' => $item['berat'],
                    ]);
                }

                if ($totalAmount === 0.0) {
                    $this->decrementStockForItems($items);
                }

                return $checkout;
            });

            $ids = collect($cart)->map(fn (Keranjang $item) => $item->id_keranjang)->all();
            $customer->keranjangs()->whereIn('id_keranjang', $ids)->delete();
            Session::forget('checkout_ids');

            if ($totalAmount > 0) {
                $snapToken = $this->midtrans->createSnapToken($this->snapPayload($checkout, $items, $shippingCost, $diskonNominal));
                $checkout->update(['snap_token' => $snapToken]);
            }

            return redirect()->route('checkout.show', $checkout->id_checkout);
        } catch (Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['kode_diskon' => $e->getMessage()])->withInput();
        }
    }

    public function show(int $id): View
    {
        $checkout = $this->ownedCheckout($id);
        $this->reconcileAutoStatuses($checkout);
        $this->reconcileShipping($checkout);

        return view('customer.checkout.show', compact('checkout'));
    }

    public function confirm(int $id): RedirectResponse
    {
        $checkout = $this->ownedCheckout($id);
        $this->reconcileShipping($checkout);

        if ($checkout->status !== 'delivered') {
            return back()->withErrors(['status' => 'Pesanan belum sampai di tujuan.']);
        }

        $checkout->update(['status' => 'completed']);

        return redirect()->route('checkout.show', $checkout->id_checkout)->with('status', 'Pesanan diselesaikan. Terima kasih!');
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $checkout = $this->ownedCheckout($id);

        if (! in_array($checkout->status, Checkout::cancellableStatuses(), true)) {
            return back()->withErrors(['cancel' => 'Pesanan tidak bisa dibatalkan.']);
        }

        if ($checkout->status === 'pending') {
            $checkout->update(['status' => 'cancelled']);

            return redirect()->route('checkout.show', $checkout->id_checkout)
                ->with('status', 'Pesanan dibatalkan.');
        }

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:255'],
        ]);

        $checkout->update([
            'status' => 'cancel_pending',
            'cancel_from' => $checkout->status,
            'cancel_reason' => trim($data['cancel_reason']),
            'cancel_requested_at' => now(),
        ]);

        return redirect()->route('checkout.show', $checkout->id_checkout)
            ->with('status', 'Permintaan pembatalan dikirim. Menunggu konfirmasi admin.');
    }

    public function history(): View
    {
        $customer = $this->customer();

        $checkouts = $customer->checkouts()
            ->with('items')
            ->latest('id_checkout')
            ->get();

        foreach ($checkouts->where('status', 'pending') as $checkout) {
            $this->reconcileMidtrans($checkout);
        }

        foreach ($checkouts->whereIn('status', ['shipping', 'delivered']) as $checkout) {
            $this->reconcileShipping($checkout);
        }

        foreach ($checkouts as $checkout) {
            $this->reconcileAutoStatuses($checkout);
        }

        $checkouts = $customer->checkouts()
            ->with('items')
            ->latest('id_checkout')
            ->get();

        return view('customer.checkout.history', compact('checkouts'));
    }

    public function notification(Request $request): JsonResponse
    {
        $ok = $this->midtrans->verifySignature(
            $request->string('order_id'),
            $request->string('status_code'),
            $request->string('gross_amount'),
            $request->string('signature_key'),
        );

        if (! $ok) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $checkout = Checkout::where('order_id', $request->string('order_id'))->first();

        if (! $checkout || abs((float) $checkout->total_amount - (float) $request->input('gross_amount')) > 0.01) {
            return response()->json(['message' => 'Order not found'], 200);
        }

        $this->applyMidtransStatus($checkout, $request->string('transaction_status'), $request->string('payment_type'));

        return response()->json(['message' => 'OK']);
    }

    /** @return Collection<int, Keranjang> */
    protected function cart()
    {
        $ids = collect(Session::get('checkout_ids', []))->map('intval')->all();

        return $this->customer()->keranjangs()
            ->when($ids !== [], fn ($q) => $q->whereIn('id_keranjang', $ids))
            ->with([
                'barang' => fn ($q) => $q->withTrashed()->with('ukurans'),
                'ukuran' => fn ($q) => $q->withTrashed(),
            ])
            ->latest('id_keranjang')
            ->get()
            ->filter(fn (Keranjang $item) => $this->canUseCartItem($item))
            ->values();
    }

    /** @return array<int, array<string, mixed>> */
    protected function cartItems($cart): array
    {
        return $cart->map(function (Keranjang $item): array {
            $unitPrice = (float) ($item->ukuran?->harga_ukuran ?? $item->barang->harga);
            $berat = $this->itemBerat($item);

            return [
                'barang' => $item->barang,
                'ukuran' => $item->ukuran,
                'ukuran_name' => $item->ukuran
                    ? trim($item->ukuran->nama_ukuran.' '.$item->ukuran->ukuran)
                    : null,
                'unit_price' => $unitPrice,
                'jumlah_barang' => (int) $item->jumlah_barang,
                'subtotal' => round($unitPrice * (int) $item->jumlah_barang, 2),
                'berat' => round($berat * (int) $item->jumlah_barang, 3),
            ];
        })->all();
    }

    protected function itemBerat(Keranjang $item): float
    {
        return ((float) $item->barang->berat) > 0 ? (float) $item->barang->berat : 1.0;
    }

    protected function cartWeightKg(): int
    {
        return max(1, (int) round($this->sum($this->cartItems($this->cart()), 'berat')));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeRates(array $rates): array
    {
        // Klikresi returns the list under a `pricing` key (array of flat service rows).
        // Older/nested shapes are also supported for safety.
        $rows = $rates['pricing'] ?? null;

        if (! is_array($rows)) {
            $rows = (isset($rates['service']) && is_array($rates['service']) === false) || isset($rates['price'])
                ? [$rates]
                : $rates;
        }

        $courier = strtolower(strval(config('services.klikresi.courier', 'jnt')));
        $options = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            foreach ($row['services'] ?? [$row] as $service) {
                $code = strtolower(strval($service['courier_code'] ?? ''));

                if ($code !== '' && $courier !== '' && $code !== $courier) {
                    continue;
                }

                $options[] = [
                    'id' => $service['service'] ?? '',
                    'service' => $service['service'] ?? '',
                    'description' => $service['type'] ?? $service['description'] ?? '',
                    'cost' => (float) ($service['price'] ?? 0),
                    'etd' => $service['duration'] ?? $service['etd'] ?? '',
                ];
            }
        }

        return array_values(array_filter($options, fn ($o) => $o['id'] !== '' && $o['cost'] > 0));
    }

    protected function verifyShippingCost(Alamat $alamat, float $submitted, string $service): float
    {
        $options = $this->normalizeRates(
            $this->klikresi->rate(config('services.klikresi.origin_id'), (string) $alamat->id_kecamatan, $this->cartWeightKg())
        );

        $match = collect($options)->first(fn ($o) => $o['id'] === $service);

        if (! $match) {
            throw new RuntimeException('Layanan pengiriman tidak valid.');
        }

        if (abs((float) $match['cost'] - $submitted) > 0.01) {
            throw new RuntimeException('Ongkir tidak valid. Silakan muat ulang halaman.');
        }

        return (float) $match['cost'];
    }

    /** @return array{0: string|null, 1: float} */
    protected function verifyDiscount(float $subtotal, ?string $kodeDiskon): array
    {
        if ($kodeDiskon === null || trim($kodeDiskon) === '') {
            return [null, 0.0];
        }

        $diskon = Diskon::where('kode_diskon', trim($kodeDiskon))
            ->where('status_diskon', 'aktif')
            ->where('mulai_diskon', '<=', now())
            ->where('akhir_diskon', '>=', now())
            ->first();

        if (! $diskon) {
            throw new RuntimeException('Kode diskon tidak valid atau kadaluarsa.');
        }

        $nominal = round($subtotal * ((float) $diskon->jumlah_diskon / 100), 2);

        return [$diskon->kode_diskon, min($nominal, $subtotal)];
    }

    /** @return array<string, mixed> */
    protected function snapPayload(Checkout $checkout, array $items, float $shippingCost, float $diskonNominal): array
    {
        $itemDetails = [];

        foreach ($items as $item) {
            $itemDetails[] = [
                'id' => (string) $item['barang']->id_barang,
                'price' => $item['unit_price'],
                'quantity' => $item['jumlah_barang'],
                'name' => Str::limit($item['barang']->nama_barang, 48),
            ];
        }

        if ($shippingCost > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING_JNT',
                'price' => $shippingCost,
                'quantity' => 1,
                'name' => 'Ongkir J&T '.$checkout->shipping_service,
            ];
        }

        if ($diskonNominal > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -$diskonNominal,
                'quantity' => 1,
                'name' => 'Diskon',
            ];
        }

        return [
            'transaction_details' => [
                'order_id' => $checkout->order_id,
                'gross_amount' => (int) round($checkout->total_amount),
            ],
            'customer_details' => [
                'first_name' => $checkout->customer_name,
                'email' => $checkout->customer_email,
                'phone' => $checkout->customer_telp,
            ],
            'item_details' => $itemDetails,
            // ponytail: Midtrans discount-as-negative-item encoding verified live with a sandbox key.
        ];
    }

    protected function applyMidtransStatus(Checkout $checkout, string $transactionStatus, string $paymentType): void
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

    protected function decrementStock(Checkout $checkout): void
    {
        $this->decrementStockForItems($checkout->items);
    }

    /** @param iterable<array{id_ukuran?: int|null, id_barang: int, jumlah_barang: int}>|Illuminate\Database\Eloquent\Collection<int, CheckoutItem> $rows */
    protected function decrementStockForItems($rows): void
    {
        foreach ($rows as $item) {
            $idUkuran = $item['id_ukuran'] ?? $item->id_ukuran ?? null;
            $idBarang = $item['id_barang'] ?? $item->id_barang;
            $jumlah = (int) ($item['jumlah_barang'] ?? $item->jumlah_barang);

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

    protected function ownedAlamat(int $id): Alamat
    {
        return $this->customer()->alamats()->findOrFail($id);
    }

    protected function ownedCheckout(int $id): Checkout
    {
        $checkout = $this->customer()->checkouts()
            ->with(['items', 'alamat'])
            ->findOrFail($id);

        if ($checkout->status === 'pending') {
            $this->reconcileMidtrans($checkout);
        }

        return $checkout;
    }

    protected function reconcileAutoStatuses(Checkout $checkout): void
    {
        if ($checkout->status === 'pending' && $checkout->created_at->lt(now()->subHours(24))) {
            $checkout->update(['status' => 'cancelled']);
        }

        if ($checkout->status === 'paid' && $checkout->paid_at && $checkout->paid_at->lt(now()->subDays(3))) {
            $this->restockAndRefund($checkout, 'Pesanan dibatalkan otomatis, pembayaran belum dikonfirmasi 3 hari.');
        }
    }

    protected function restockAndRefund(Checkout $checkout, string $reason): void
    {
        try {
            $this->midtrans->refund($checkout->order_id, (float) $checkout->total_amount, $reason);
        } catch (Throwable $e) {
            logger()->warning('Refund failed for checkout '.$checkout->id_checkout.': '.$e->getMessage());
        }

        $checkout->restoreStock();
        $checkout->update(['status' => 'refunded']);
    }

    protected function reconcileMidtrans(Checkout $checkout): void
    {
        try {
            $data = $this->midtrans->transactionStatus($checkout->order_id);
        } catch (Throwable) {
            return;
        }

        $this->applyMidtransStatus($checkout, strval($data['transaction_status'] ?? ''), strval($data['payment_type'] ?? ''));
    }

    protected function reconcileShipping(Checkout $checkout): void
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

    protected function addressLabel(Alamat $alamat): string
    {
        return implode(', ', array_filter([
            $alamat->detail_alamat,
            trim($alamat->kelurahan.' '.$alamat->kecamatan),
            $alamat->kota,
            $alamat->provinsi,
            $alamat->kode_pos,
        ]));
    }

    protected function orderId(): string
    {
        return 'ORD-'.strtoupper(bin2hex(random_bytes(5)));
    }

    protected function canUseCartItem(Keranjang $keranjang): bool
    {
        if (! $keranjang->barang || $keranjang->barang->trashed()) {
            return false;
        }

        if ($keranjang->barang->status !== 'Ditampilkan') {
            return false;
        }

        return ! $keranjang->ukuran || ! $keranjang->ukuran->trashed();
    }

    /** @param array<int, array<string, mixed>> $rows */
    protected function sum(array $rows, string $key): float
    {
        return round(array_sum(array_column($rows, $key)), 2);
    }

    protected function customer(): Customer
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return $customer;
    }
}
