<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Services\CheckoutStatusService;
use App\Services\KlikresiApi;
use App\Services\MidtransApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PesananController extends Controller
{
    public function __construct(
        protected KlikresiApi $klikresi,
        protected MidtransApi $midtrans,
        protected CheckoutStatusService $status,
    ) {}

    public function listPesanan(Request $request): View
    {
        $filter = trim((string) $request->query('status', ''));

        $query = Checkout::query()
            ->with(['items', 'pegawai'])
            ->when($filter !== '' && array_key_exists($filter, Checkout::STATUSES), fn ($q) => $q->where('status', $filter));

        $all = $query->get();

        foreach ($all as $checkout) {
            $this->status->reconcile($checkout);
        }

        $pesanan = Checkout::query()
            ->with(['items', 'pegawai'])
            ->when($filter !== '' && array_key_exists($filter, Checkout::STATUSES), fn ($q) => $q->where('status', $filter))
            ->latest('id_checkout')
            ->paginate(10)
            ->withQueryString();

        return view('pegawai.pesanan.k_pesanan', compact('pesanan', 'filter'));
    }

    public function detailPesanan(int $id): View
    {
        $checkout = Checkout::with(['items', 'customer', 'pegawai'])->findOrFail($id);
        $this->status->reconcile($checkout);
        $tracking = $this->status->trackingFor($checkout);
        $trackingFake = (bool) config('services.klikresi.tracking_fake');

        $u = strtoupper((string) $checkout->no_resi);
        $fakeResi = str_contains($u, 'DEL') || str_contains($u, 'TRK') || str_contains($u, 'PIC');

        return view('pegawai.pesanan.detailPesanan', compact('checkout', 'tracking', 'trackingFake', 'fakeResi'));
    }

    public function proccessRequest(Request $request, int $id): RedirectResponse
    {
        $checkout = Checkout::findOrFail($id);

        if ($checkout->status !== 'paid') {
            return back()->withErrors(['status' => 'Pesanan tidak bisa diproses.']);
        }

        $checkout->update([
            'status' => 'processed',
            'id_pegawai' => (int) auth('pegawai')->id(),
        ]);

        return redirect()->route('pegawai.pesanan')->with('status', 'Pesanan diterima dan sedang diproses.');
    }

    public function kirim(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'no_resi' => ['required', 'string', 'max:50'],
        ]);

        $checkout = Checkout::findOrFail($id);

        if ($checkout->status === 'shipping') {
            return back()->withErrors(['no_resi' => 'Pesanan sudah dalam pengiriman.']);
        }

        try {
            $this->klikresi->tracking($data['no_resi']);
        } catch (Throwable $e) {
            return back()->withErrors(['no_resi' => $e->getMessage()])->withInput();
        }

        $checkout->update([
            'status' => 'shipping',
            'no_resi' => $data['no_resi'],
        ]);

        return redirect()->route('pegawai.pesanan')->with('status', 'No resi disimpan, pesanan dalam pengiriman.');
    }

    public function ubahTracking(Request $request, int $id): RedirectResponse
    {
        if (! (bool) config('services.klikresi.tracking_fake')) {
            return back()->withErrors(['tracking_status' => 'Fitur hanya tersedia saat TRACKING_FAKE aktif.']);
        }

        $data = $request->validate([
            'tracking_status' => ['required', 'string', 'in:delivered,in_transit,picked_up'],
        ]);

        $checkout = Checkout::findOrFail($id);

        if ($checkout->status !== 'shipping' || ! $checkout->no_resi) {
            return back()->withErrors(['tracking_status' => 'Pesanan tidak dalam pengiriman.']);
        }

        $keyword = ['delivered' => 'DEL', 'in_transit' => 'TRK', 'picked_up' => 'PIC'][$data['tracking_status']];

        $checkout->update([
            'no_resi' => $this->setTrackingKeyword($checkout->no_resi, $keyword),
        ]);

        return redirect()->route('pegawai.detailpesanan', $checkout->id_checkout)
            ->with('status', 'Status tracking diubah menjadi '.$data['tracking_status'].'.');
    }

    protected function setTrackingKeyword(string $noResi, string $keyword): string
    {
        $changed = preg_replace('/DEL|TRK|PIC/i', $keyword, $noResi, 1, $count);

        return $count > 0 ? $changed : $noResi.'-'.$keyword;
    }

    public function cancelPesanan(Request $request, int $id): RedirectResponse
    {
        if (! auth('pegawai')->user()->canInventory()) {
            return back()->withErrors(['cancel' => 'Pembatalan & refund hanya bisa dilakukan pegawai akses Inventaris.']);
        }

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:255'],
        ]);

        $checkout = Checkout::findOrFail($id);

        if (! in_array($checkout->status, ['paid', 'processed'], true)) {
            return back()->withErrors(['cancel' => 'Pesanan tidak bisa dibatalkan pada status ini.']);
        }

        if (! $this->refundCheckout($checkout, trim($data['cancel_reason']))) {
            return back()->withErrors(['cancel' => 'Refund gagal, coba lagi atau lakukan manual.']);
        }

        return redirect()->route('pegawai.detailpesanan', $checkout->id_checkout)
            ->with('status', 'Pesanan dibatalkan, dana dikembalikan ke customer.');
    }

    protected function refundCheckout(Checkout $checkout, ?string $cancelReason = null): bool
    {
        try {
            if (! $this->alreadyRefunded($checkout)) {
                $this->midtrans->refund(
                    $checkout->order_id,
                    (float) $checkout->total_amount,
                    'Pembatalan pesanan '.$checkout->order_id,
                );
            }
        } catch (Throwable $e) {
            logger()->error('Refund failed for checkout '.$checkout->id_checkout.': '.$e->getMessage());

            return false;
        }

        $checkout->restoreStock();

        $update = [
            'status' => 'refunded',
            'id_pegawai' => (int) auth('pegawai')->id(),
        ];

        if ($cancelReason !== null) {
            $update['cancel_reason'] = $cancelReason;
        }

        $checkout->update($update);

        return true;
    }

    public function cancelApprove(Request $request, int $id): RedirectResponse
    {
        if (! auth('pegawai')->user()->canInventory()) {
            return back()->withErrors(['cancel' => 'Pembatalan & refund hanya bisa dilakukan pegawai akses Inventaris.']);
        }

        $checkout = Checkout::findOrFail($id);

        if ($checkout->status !== 'cancel_pending') {
            return back()->withErrors(['cancel' => 'Pesanan tidak dalam status pembatalan.']);
        }

        if (in_array($checkout->cancel_from, ['paid', 'processed'], true)) {
            if (! $this->refundCheckout($checkout)) {
                return back()->withErrors(['cancel' => 'Refund gagal, coba lagi atau lakukan manual.']);
            }
        } else {
            $checkout->update(['status' => 'cancelled', 'id_pegawai' => (int) auth('pegawai')->id()]);
        }

        return redirect()->route('pegawai.pesanan')->with('status', 'Pembatalan pesanan disetujui.');
    }

    public function cancelReject(Request $request, int $id): RedirectResponse
    {
        if (! auth('pegawai')->user()->canInventory()) {
            return back()->withErrors(['cancel' => 'Pembatalan & refund hanya bisa dilakukan pegawai akses Inventaris.']);
        }

        $data = $request->validate([
            'cancel_response' => ['required', 'string', 'max:255'],
        ]);

        $checkout = Checkout::findOrFail($id);

        if ($checkout->status !== 'cancel_pending') {
            return back()->withErrors(['cancel' => 'Pesanan tidak dalam status pembatalan.']);
        }

        $checkout->update([
            'status' => $checkout->cancel_from ?? 'paid',
            'cancel_response' => trim($data['cancel_response']),
            'cancel_from' => null,
            'cancel_reason' => null,
            'cancel_requested_at' => null,
        ]);

        return redirect()->route('pegawai.pesanan')->with('status', 'Permintaan pembatalan ditolak.');
    }

    protected function alreadyRefunded(Checkout $checkout): bool
    {
        try {
            $data = $this->midtrans->transactionStatus($checkout->order_id);
        } catch (Throwable) {
            return false;
        }

        return in_array(strval($data['transaction_status'] ?? ''), ['refund', 'partial_refund', 'partially_refunded'], true);
    }
}
