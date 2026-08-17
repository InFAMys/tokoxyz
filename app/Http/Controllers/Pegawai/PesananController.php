<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
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
    ) {}

    public function listPesanan(Request $request): View
    {
        $filter = trim((string) $request->query('status', ''));

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

        return view('pegawai.pesanan.detailPesanan', compact('checkout'));
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

    public function cancelApprove(Request $request, int $id): RedirectResponse
    {
        $checkout = Checkout::findOrFail($id);

        if ($checkout->status !== 'cancel_pending') {
            return back()->withErrors(['cancel' => 'Pesanan tidak dalam status pembatalan.']);
        }

        if (in_array($checkout->cancel_from, ['paid', 'processed'], true)) {
            try {
                $this->midtrans->refund(
                    $checkout->order_id,
                    (float) $checkout->total_amount,
                    'Pembatalan pesanan '.$checkout->order_id,
                );
            } catch (Throwable $e) {
                logger()->warning('Refund failed for checkout '.$checkout->id_checkout.': '.$e->getMessage());
            }

            $checkout->restoreStock();
            $checkout->update(['status' => 'refunded', 'id_pegawai' => (int) auth('pegawai')->id()]);
        } else {
            $checkout->update(['status' => 'cancelled', 'id_pegawai' => (int) auth('pegawai')->id()]);
        }

        return redirect()->route('pegawai.pesanan')->with('status', 'Pembatalan pesanan disetujui.');
    }

    public function cancelReject(Request $request, int $id): RedirectResponse
    {
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
}
