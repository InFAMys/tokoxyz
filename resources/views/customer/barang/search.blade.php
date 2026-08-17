@extends('customer.layouts.app')

@section('title', 'Hasil Pencarian - Toko XYZ')

@php
    $activ = 'home';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="page-title mb-0">Hasil Pencarian</h1>
                @if ($q !== '')
                    <p class="text-muted mb-0">Pencarian untuk &ldquo;{{ $q }}&rdquo;</p>
                @endif
            </div>
            <a href="{{ route('home') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form method="GET" action="{{ route('barang.search') }}" class="d-flex gap-2 mb-4" role="search">
            <input class="form-control" type="search" name="q" value="{{ $q }}"
                placeholder="Cari produk..." aria-label="Cari produk">
            <button class="btn btn-pink" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>

        @if ($barang->isEmpty())
            <div class="card-pink p-4 text-center">
                <p class="mb-0">Tidak ada produk ditemukan.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach ($barang as $b)
                    <a href="{{ route('barang.detail', $b->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                        <div class="product-card">
                            <div class="product-img">
                                <img class="img-fluid" src="{{ asset('storage/' . $b->thumbnailPath()) }}"
                                    alt="{{ $b->nama_barang }}" style="width:8rem" />
                            </div>
                            <div class="card-body">
                                <div class="product-name">{{ $b->nama_barang }}</div>
                                <div class="product-price">Rp {{ number_format($b->harga, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <nav class="mt-4">{{ $barang->links() }}</nav>
        @endif
    </div>
@endsection
