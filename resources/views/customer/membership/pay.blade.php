@extends('customer.layouts.app')

@section('title', 'Pembayaran Member - Toko XYZ')
@php $activ = 'profil'; @endphp

@section('content')
    <div class="main-content" style="max-width: 480px; margin: 0 auto">
        <div class="position-relative text-center mb-4">
            <a href="{{ route('membership.index') }}" class="btn btn-pink-outline position-absolute start-0 top-50 translate-middle-y">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title mb-0" style="padding-inline: 5.5rem;">Daftar Member</h1>
        </div>

        @if ($errors->any())
            <div class="mb-3 text-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card-pink p-4 mb-3 text-center">
            <div class="mb-2"><i class="fa-solid fa-id-card fa-2x text-warning"></i></div>
            <h5 class="fw-bold mb-1">Keanggotaan Member</h5>
            <p class="text-muted small mb-3">Bayar sekali Rp {{ number_format(25000, 0, ',', '.') }} dan dapatkan
                <strong>diskon 10%</strong> untuk setiap pesanan selamanya.</p>
            <div class="summary-box p-3">
                <div class="summary-row">
                    <span>Biaya Member</span>
                    <span>Rp {{ number_format(25000, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>Rp {{ number_format(25000, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <form id="member-pay-form" data-token-url="{{ route('membership.token') }}">
            @csrf
            <button type="button" id="bayar-button" class="btn btn-pink w-100"
                data-client-key="{{ config('services.midtrans.client_key') }}"
                data-prod="{{ config('services.midtrans.is_production') ? 1 : 0 }}"
                data-redirect-url="{{ route('membership.index') }}">
                <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
            </button>
        </form>
        <p class="text-muted small mt-2 mb-0">Setelah pembayaran, status member akan diperbarui otomatis.</p>
    </div>
@endsection
