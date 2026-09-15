@extends('customer.layouts.app')

@section('title', $title.' - Toko XYZ')

@php
    $activ = 'home';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="page-title mb-0">{{ $title }}</h1>
            </div>
            <a href="{{ route('home') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($barang->isEmpty())
            <div class="card-pink p-4 text-center">
                <p class="mb-0">Tidak ada produk ditemukan.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach ($barang as $b)
                    <a href="{{ route('barang.detail', $b->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                        <div class="product-card">
                            <div class="product-img position-relative">
                                <img class="img-fluid" src="{{ asset('storage/' . $b->thumbnailPath()) }}"
                                    alt="{{ $b->nama_barang }}" />
                                @if ($b->stokReady() < 1)
                                    @if ($b->preorder === 'Tersedia')
                                        <span class="position-absolute top-0 start-0 badge text-bg-warning" style="font-size:0.65em">Preorder</span>
                                    @else
                                        <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                            style="opacity:.85">Habis</span>
                                    @endif
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="product-name">{{ $b->nama_barang }}</div>
                                <div class="product-price">
                                    @php
                                        $harga = $b->ukurans->pluck('harga_ukuran')->filter()->map(fn ($p) => (float) $p)->min();
                                    @endphp
                                    Rp {{ number_format($harga ?? $b->harga, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <nav class="mt-4">{{ $barang->links() }}</nav>
        @endif
    </div>
@endsection
