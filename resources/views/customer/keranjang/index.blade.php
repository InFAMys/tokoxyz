@extends('customer.layouts.app')

@section('title', 'Keranjang - Toko XYZ')

@php
    $activ = 'keranjang';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <p class="text-pink fw-semibold mb-1">Belanja</p>
                <h1 class="page-title mb-0">Keranjang Saya</h1>
            </div>
            <a href="{{ route('home') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                <i class="fa-solid fa-arrow-left"></i> Lanjut Belanja
            </a>
        </div>

        {{-- @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif --}}

        {{-- @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif --}}

        @if ($keranjang->isEmpty())
            <div class="card-pink p-5 text-center">
                <i class="fa-solid fa-shopping-cart text-pink mb-3" style="font-size: 3rem;"></i>
                <h2 class="h4 mb-2">Keranjang masih kosong</h2>
                <p class="text-muted mb-4">Pilih barang yang ingin dibeli lalu masukkan ke keranjang.</p>
                <a href="{{ route('home') }}" class="btn btn-pink">
                    <i class="fa-solid fa-shop"></i> Mulai Belanja
                </a>
            </div>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    @foreach ($keranjang as $item)
                        @php
                            $barang = $item->barang;
                            $isAvailable =
                                $barang &&
                                !$barang->trashed() &&
                                $barang->status === 'Ditampilkan' &&
                                (!$item->ukuran || !$item->ukuran->trashed());
                            $imagePath = $barang?->thumbnailPath();
                            $stockReady = $isAvailable
                                ? ($item->ukuran
                                    ? $item->ukuran->stok_ukuran
                                    : $barang->stok)
                                : 0;
                            $subtotal = $isAvailable ? (float) ($item->ukuran?->harga_ukuran ?? $barang->harga) * $item->jumlah_barang : 0;
                        @endphp

                        <div class="card-pink p-3 mb-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-4 col-md-2">
                                    @if ($isAvailable)
                                        <a href="{{ route('barang.detail', $barang->id_barang) }}"
                                            class="text-decoration-none">
                                        @else
                                            <div>
                                    @endif
                                    @if ($imagePath)
                                        <img src="{{ asset('storage/' . $imagePath) }}"
                                            alt="{{ $barang?->nama_barang ?? 'Barang' }}" class="w-100 rounded"
                                            style="aspect-ratio: 1 / 1; object-fit: cover;">
                                    @else
                                        <div class="product-detail-img" style="aspect-ratio: 1 / 1; height: auto;">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                    @if ($isAvailable)
                                        </a>
                                    @else
                                </div>
                    @endif
                </div>

                <div class="col-8 col-md-4">
                    @if ($isAvailable)
                        <a href="{{ route('barang.detail', $barang->id_barang) }}" class="text-decoration-none text-dark">
                            <h2 class="h6 fw-bold mb-1">{{ $barang->nama_barang }}</h2>
                        </a>
                    @else
                        <h2 class="h6 fw-bold mb-1 text-muted">{{ $barang?->nama_barang ?? 'Barang tidak tersedia' }}</h2>
                    @endif
                    <p class="small text-muted mb-1">
                        {{ $barang?->brand?->nama_brand ?? '-' }} ·
                        {{ $barang?->kategori?->nama_kategori ?? '-' }}
                    </p>
                    @if ($item->ukuran)
                        <p class="small mb-1">
                            Ukuran: {{ $item->ukuran->nama_ukuran }}
                            @if ($item->ukuran->ukuran)
                                - {{ $item->ukuran->ukuran }}
                            @endif
                        </p>
                    @endif
                    @if ($isAvailable)
                        <p class="small text-muted mb-0">Stok: {{ $stockReady }}</p>
                    @else
                        <p class="small text-danger mb-0">Barang tidak tersedia.</p>
                    @endif
                </div>

                <div class="col-md-2">
                    <div class="small text-muted">Harga</div>
                    <div class="fw-semibold text-pink">
                        {{ $isAvailable ? 'Rp ' . number_format($item->ukuran?->harga_ukuran ?? $barang->harga, 0, ',', '.') : '-' }}
                    </div>
                </div>

                <div class="col-md-2">
                    @if ($isAvailable)
                        <form method="POST" action="{{ route('keranjang.update', $item->id_keranjang) }}"
                            class="d-flex gap-2" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="flex-grow-1 position-relative">
                                <input type="number" name="jumlah_barang" min="1" max="{{ $stockReady }}"
                                    value="{{ $item->jumlah_barang }}" class="form-control form-control-sm"
                                    required>
                                <small class="text-danger d-none position-absolute"
                                    style="top:100%;left:0" data-jumlah-error></small>
                            </div>
                            <button type="submit" class="btn btn-sm btn-pink-outline" aria-label="Perbarui jumlah">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    @else
                        <span class="text-muted small">Tidak dapat diperbarui</span>
                    @endif
                </div>

                <div class="col-md-2">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <div class="small text-muted">Subtotal</div>
                            <div class="fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteKeranjangModal-{{ $item->id_keranjang }}" aria-label="Hapus barang">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
    </div>

    <div class="modal fade" id="deleteKeranjangModal-{{ $item->id_keranjang }}" tabindex="-1"
        aria-labelledby="deleteKeranjangModal-{{ $item->id_keranjang }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-pink">
                <div class="modal-header">
                    <h1 class="modal-title fs-4" id="deleteKeranjangModal-{{ $item->id_keranjang }}Label">
                        Hapus Barang?
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center my-4">
                    {{ $barang?->nama_barang ?? 'Barang ini' }} akan dihapus dari keranjang Anda.
                </div>
                <div class="modal-footer mx-auto">
                    <form method="POST" action="{{ route('keranjang.destroy', $item->id_keranjang) }}">
                        @csrf
                        <button type="submit" class="btn btn-delete btn-sm">
                            <i class="fa-solid fa-trash"></i> HAPUS
                        </button>
                    </form>
                    <button type="button" class="btn btn-green btn-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> BATAL
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </div>

    <div class="col-lg-4">
        <div class="summary-box">
            <h2 class="h5 mb-3">Ringkasan</h2>
            <div class="summary-row">
                <span>Total Barang</span>
                <span>{{ $keranjang->sum('jumlah_barang') }}</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button type="button" class="btn btn-pink w-100 mt-3" disabled>
                <i class="fa-solid fa-receipt"></i> Checkout
            </button>
        </div>
    </div>
    </div>
    @endif
    </div>
@endsection

