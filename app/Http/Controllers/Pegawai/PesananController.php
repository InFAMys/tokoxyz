<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Services\KlikresiApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PesananController extends Controller
{
    public const PROGRESS = ['paid', 'processed', 'shipping', 'delivered', 'completed'];

    public function __construct(protected KlikresiApi $klikresi) {}

    public function listPesanan(Request $request): View
    {
        $filter = trim((string) $request->query('status', ''));

        $pesanan = Checkout::query()
            ->with(['items', 'pegawai'])
            ->when($filter !== '' && in_array($filter, self::PROGRESS, true), fn ($q) => $q->where('status', $filter))
            ->latest('id_checkout')
            ->get();

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
}
