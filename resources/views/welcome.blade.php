@extends('customer.layouts.app')

@section('title', 'Toko XYZ')
@php
    $activ = 'home';
@endphp

@section('content')
    <div class="main-content">
        <!-- Hero -->
        <div class="hero-banner">
            <h2>Selamat Datang di Toko XYZ <i class="fa-solid fa-shop"></i></h2>
            <p class="mb-3" style="opacity: 0.85">
                Temukan produk terbaik dengan harga terjangkau
            </p>
            <form method="GET" action="{{ route('barang.search') }}" class="d-flex gap-2 mb-3" role="search">
                <input class="form-control" type="search" name="q" placeholder="Cari produk..."
                    aria-label="Cari produk">
                <button class="btn" type="submit"
                    style="background:#fff;color:var(--pink-600);font-weight:700;border-radius:25px">Cari</button>
            </form>
            <button class="btn"
                style="
            background: #fff;
            color: var(--pink-600);
            font-weight: 700;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
          ">
                Belanja Sekarang
            </button>
        </div>

        <!-- Produk Baru -->
        <h5 class="fw-bold text-pink mb-3">Produk Baru</h5>
        <div class="row g-3 mb-4">
            @foreach ($barangNew as $bn)
                <a href="{{ route('barang.detail', $bn->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                    <div class="product-card">
                        <div class="product-img position-relative">
                            <img class="img-fluid" src="{{ asset('storage/' . $bn->thumbnailPath()) }}"
                                alt="{{ $bn->nama_barang }}" />
                            @if ($bn->stokReady() < 1)
                                <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                    style="opacity:.85">Habis</span>
                            @endif
                        </div>
                        <div class="card-body">
                            {{-- <div class="product-name">{{ Str::limit($bn->nama_barang, 20) }}</div> --}}
                            <div class="product-name">{{ $bn->nama_barang }}</div>
                            <div class="product-price">
                                @php
                                    $harga = $bn->ukurans->pluck('harga_ukuran')->filter()->map(fn ($p) => (float) $p)->min();
                                @endphp
                                Rp {{ number_format($harga ?? $bn->harga, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <h5 class="fw-bold text-pink mb-3">Rekomendasi Untukmu</h5>
        <div class="row g-3">
            @foreach ($barangRand as $br)
                <a href="{{ route('barang.detail', $br->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                    <div class="product-card">
                        <div class="product-img position-relative">
                            <img class="img-fluid" src="{{ asset('storage/' . $br->thumbnailPath()) }}"
                                alt="{{ $br->nama_barang }}" />
                            @if ($br->stokReady() < 1)
                                <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                    style="opacity:.85">Habis</span>
                            @endif
                        </div>
                        <div class="card-body">
                            {{-- <div class="product-name">{{ Str::limit($br->nama_barang, 20) }}</div> --}}
                            <div class="product-name">{{ $br->nama_barang }}</div>
                            <div class="product-price">
                                @php
                                    $harga = $br->ukurans->pluck('harga_ukuran')->filter()->map(fn ($p) => (float) $p)->min();
                                @endphp
                                Rp {{ number_format($harga ?? $br->harga, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
