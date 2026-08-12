<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Keranjang;
use App\Models\Ukuran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function index(): View
    {
        $keranjang = $this->customer()->keranjangs()
            ->with([
                'barang' => fn ($query) => $query->withTrashed()->with(['brand', 'kategori']),
                'ukuran' => fn ($query) => $query->withTrashed(),
            ])
            ->latest('id_keranjang')
            ->get();

        $total = $keranjang->sum(function (Keranjang $item): float {
            if (! $this->canUseCartItem($item)) {
                return 0;
            }

            return (float) ($item->ukuran?->harga_ukuran ?? $item->barang->harga) * $item->jumlah_barang;
        });

        return view('customer.keranjang.index', compact('keranjang', 'total'));
    }

    public function checkoutSelected(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_keranjang' => ['required', 'array', 'min:1'],
            'id_keranjang.*' => ['integer'],
        ]);

        $ids = $this->customer()->keranjangs()
            ->whereIn('id_keranjang', array_map('intval', $data['id_keranjang']))
            ->pluck('id_keranjang')
            ->all();

        if ($ids === []) {
            return back()->withErrors(['id_keranjang' => 'Pilih minimal satu barang untuk checkout.']);
        }

        Session::put('checkout_ids', $ids);

        return redirect()->route('checkout.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_barang' => ['required', 'integer'],
            'id_ukuran' => ['nullable', 'integer'],
            'jumlah_barang' => ['required', 'integer', 'min:1'],
        ]);

        $barang = Barang::with('ukurans')
            ->where('status', 'Ditampilkan')
            ->findOrFail($data['id_barang']);
        $ukuran = $this->selectedUkuran($barang, $data['id_ukuran'] ?? null);
        $customer = $this->customer();

        $keranjang = $customer->keranjangs()
            ->where('id_barang', $barang->id_barang)
            ->where('id_ukuran', $ukuran?->id_ukuran)
            ->first();

        $jumlahBarang = (int) $data['jumlah_barang'] + ($keranjang?->jumlah_barang ?? 0);

        $this->ensureStockIsAvailable($barang, $ukuran, $jumlahBarang);

        if ($keranjang) {
            $keranjang->update(['jumlah_barang' => $jumlahBarang]);
        } else {
            $customer->keranjangs()->create([
                'id_barang' => $barang->id_barang,
                'id_ukuran' => $ukuran?->id_ukuran,
                'jumlah_barang' => $jumlahBarang,
            ]);
        }

        return redirect()->route('keranjang.index')->with('status', 'Barang berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'jumlah_barang' => ['required', 'integer', 'min:1'],
        ]);

        $keranjang = $this->customer()->keranjangs()
            ->with([
                'barang' => fn ($query) => $query->withTrashed()->with('ukurans'),
                'ukuran' => fn ($query) => $query->withTrashed(),
            ])
            ->findOrFail($id);

        if (! $this->canUseCartItem($keranjang)) {
            throw ValidationException::withMessages([
                'jumlah_barang' => 'Barang tidak tersedia.',
            ]);
        }

        $this->ensureStockIsAvailable($keranjang->barang, $keranjang->ukuran, (int) $data['jumlah_barang']);

        $keranjang->update([
            'jumlah_barang' => $data['jumlah_barang'],
        ]);

        return redirect()->route('keranjang.index')->with('status', 'Keranjang berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->customer()->keranjangs()->findOrFail($id)->delete();

        return redirect()->route('keranjang.index')->with('status', 'Barang berhasil dihapus dari keranjang.');
    }

    private function selectedUkuran(Barang $barang, ?int $idUkuran): ?Ukuran
    {
        if ($barang->ukurans->isEmpty()) {
            return null;
        }

        if (! $idUkuran) {
            throw ValidationException::withMessages([
                'id_ukuran' => 'Pilih ukuran terlebih dahulu.',
            ]);
        }

        $ukuran = $barang->ukurans->firstWhere('id_ukuran', $idUkuran);

        if (! $ukuran) {
            throw ValidationException::withMessages([
                'id_ukuran' => 'Ukuran tidak tersedia untuk barang ini.',
            ]);
        }

        return $ukuran;
    }

    private function ensureStockIsAvailable(Barang $barang, ?Ukuran $ukuran, int $jumlahBarang): void
    {
        $stokTersedia = $ukuran ? (int) $ukuran->stok_ukuran : (int) $barang->stok;

        if ($jumlahBarang > $stokTersedia) {
            throw ValidationException::withMessages([
                'jumlah_barang' => 'Jumlah barang melebihi stok tersedia.',
            ]);
        }
    }

    private function canUseCartItem(Keranjang $keranjang): bool
    {
        if (! $keranjang->barang || $keranjang->barang->trashed()) {
            return false;
        }

        if ($keranjang->barang->status !== 'Ditampilkan') {
            return false;
        }

        return ! $keranjang->ukuran || ! $keranjang->ukuran->trashed();
    }

    private function customer(): Customer
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return $customer;
    }
}
