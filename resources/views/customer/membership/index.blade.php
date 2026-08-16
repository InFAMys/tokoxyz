@extends('customer.layouts.app')

@section('title', 'Member - Toko XYZ')
@php $activ = 'profil'; @endphp

@section('content')
    <div class="main-content" style="max-width: 560px; margin: 0 auto">
        <div class="position-relative text-center mb-4">
            <a href="{{ route('profil') }}" class="btn btn-pink-outline position-absolute start-0 top-50 translate-middle-y">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title mb-0">Member</h1>
        </div>

        @if ($errors->any())
            <div class="mb-3 text-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card-pink p-4 text-center mb-3">
            <div class="mb-2">
                <i class="fa-solid fa-id-card fa-2x {{ $customer->member === 'true' ? 'text-warning' : 'text-muted' }}"></i>
            </div>
            @if ($customer->member === 'true')
                <h5 class="fw-bold mb-1">Member Aktif</h5>
                <p class="text-success small mb-0">Diskon 10% berlaku untuk setiap pesanan selamanya.</p>
                <p class="text-muted small mt-2 mb-0">Menjadi member sejak
                    {{ optional($customer->member_since)->translatedFormat('d F Y') }}</p>
            @else
                <h5 class="fw-bold mb-1">Belum Menjadi Member</h5>
                <p class="text-muted small mb-3">Nikmati diskon 10% untuk setiap pesanan selamanya.</p>
            @endif
        </div>

        <div class="card-pink p-4 mb-3">
            <div class="form-label-pink mb-2">Keuntungan Member</div>
            <ul class="mb-0 ps-3">
                <li class="mb-1">Diskon <strong>10%</strong> untuk semua produk.</li>
                <li class="mb-1">Berlaku untuk <strong>setiap pesanan</strong>, tanpa batas.</li>
                <li>Cukup bayar <strong>sekali</strong>, selamanya.</li>
            </ul>
        </div>

        @if ($customer->member === 'false')
            <a href="{{ route('membership.subscribe') }}" class="btn btn-pink w-100 d-block">
                <i class="fa-solid fa-id-card"></i> Jadi Member — Rp {{ number_format(25000, 0, ',', '.') }}
            </a>
        @endif
    </div>
@endsection
