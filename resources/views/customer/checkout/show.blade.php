@extends('customer.layouts.app')

@section('title', 'Checkout #' . $checkout->order_id . ' - Toko XYZ')
@php $activ = 'pesanan'; @endphp

@section('content')
    <div class="main-content">
        <div class="mb-4">
            <a href="{{ route('checkout.history') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>

        <div class="card-pink p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0">Pesanan {{ $checkout->order_id }}</h1>
                <span class="badge rounded-pill text-bg-{{ $checkout->status === 'paid' ? 'success' : ($checkout->status === 'pending' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($checkout->status) }}
                </span>
            </div>

            <div class="summary-box mb-3">
                <div class="form-label-pink">Alamat Pengiriman</div>
                <div>{{ $checkout->shipping_address }}</div>
                <div class="small text-muted">
                    {{ $checkout->shipping_courier }} {{ $checkout->shipping_service }}
                    @if ($checkout->berat_total > 0)
                        · {{ rtrim(rtrim(number_format($checkout->berat_total, 3, ',', '.'), '0'), ',') }} kg
                    @endif
                </div>
            </div>

            <div class="summary-box mb-3">
                <div class="form-label-pink">Barang</div>
                @foreach ($checkout->items as $item)
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ $item->nama_barang }} × {{ $item->jumlah_barang }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="summary-box mb-3">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($checkout->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($checkout->diskon_nominal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Ongkir</span>
                    <span>Rp {{ number_format($checkout->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>Rp {{ number_format($checkout->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($checkout->status === 'pending' && $checkout->snap_token)
                <button type="button" id="bayar-button" class="btn btn-pink w-100"
                    data-checkout-token="{{ $checkout->snap_token }}"
                    data-client-key="{{ config('services.midtrans.client_key') }}"
                    data-prod="{{ config('services.midtrans.is_production') ? 1 : 0 }}">
                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                </button>
                <p class="text-muted small mt-2 mb-0">Setelah pembayaran, status akan diperbarui otomatis.</p>
            @elseif ($checkout->status === 'pending')
                <div class="alert alert-info mb-0">Pembayaran belum bisa dimulai kembali. Hubungi admin.</div>
            @else
                <div class="alert alert-success mb-0">
                    <i class="fa-solid fa-circle-check"></i> Status pesanan: {{ ucfirst($checkout->status) }}
                </div>
            @endif
        </div>
    </div>
@endsection
