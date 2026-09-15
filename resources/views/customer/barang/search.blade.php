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

            <form method="GET" action="{{ route('barang.search') }}" class="mb-4" role="search">
            <div class="d-flex flex-column flex-sm-row gap-2">
                <input class="form-control" type="search" name="q" value="{{ $q }}"
                    placeholder="Cari produk..." aria-label="Cari produk">
                <button class="btn btn-pink" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            </div>
            <div class="card-pink p-3 mt-3">
                <button type="button" class="btn btn-pink-outline w-100 d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#filterHarga" aria-expanded="false">
                    <span>Filter Harga</span>
                    <i class="fa-solid fa-filter"></i>
                </button>
                <div id="filterHarga" class="collapse">
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <label class="text-muted small">Harga Min</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input class="form-control" type="text" inputmode="numeric" name="min"
                                    value="{{ $min }}" placeholder="Min" aria-label="Harga min">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Harga Maks</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input class="form-control" type="text" inputmode="numeric" name="max"
                                    value="{{ $max }}" placeholder="Maks" aria-label="Harga maks">
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button class="btn btn-pink btn-sm w-100" type="submit">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                                    alt="{{ $b->nama_barang }}" />
                            </div>
                            <div class="card-body">
                                <div class="product-name">{{ $b->nama_barang }}</div>
                                <div class="product-price">@include('customer.partials.harga', ['item' => $b])</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <nav class="mt-4">{{ $barang->links() }}</nav>
        @endif
    </div>
@endsection
