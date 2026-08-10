@extends('customer.layouts.app')

@section('title', $barang->nama_barang . ' - Toko XYZ')

@php
    $activ = 'home';
    $galleryImages = collect([$barang->thumbnail])
        ->merge($barang->foto ?? [])
        ->filter()
        ->unique()
        ->values();
    $mainImage = $galleryImages->first();
    $hasUkuran = $barang->ukurans->isNotEmpty();
    $stokReady = $hasUkuran ? $barang->ukurans->sum('stok_ukuran') : $barang->stok;
    $formattedBerat = !is_null($barang->berat)
        ? rtrim(rtrim(number_format((float) $barang->berat, 3, ',', '.'), '0'), ',') . ' kg'
        : '-';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <p class="text-pink fw-semibold mb-1">{{ $barang->kode_barang }}</p>
                <h1 class="page-title mb-0">{{ $barang->nama_barang }}</h1>
            </div>
            <a href="{{ route('home') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-pink p-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    @if ($mainImage)
                        <div class="product-detail-img overflow-hidden">
                            <img id="detail-main-image" src="{{ asset('storage/' . $mainImage) }}"
                                alt="{{ $barang->nama_barang }}" class="w-100" style="height: 420px; object-fit: cover;">
                        </div>
                    @else
                        <div class="product-detail-img" style="height: 420px;">
                            <i class="fa-solid fa-image"></i>
                        </div>
                    @endif

                    @if ($galleryImages->count() > 1)
                        <div class="thumb-row flex-wrap">
                            @foreach ($galleryImages as $foto)
                                <button type="button" class="thumb {{ $loop->first ? 'active' : '' }} overflow-hidden p-0"
                                    data-photo="{{ asset('storage/' . $foto) }}"
                                    data-photo-alt="Foto {{ $loop->iteration }} {{ $barang->nama_barang }}"
                                    aria-label="Tampilkan foto {{ $loop->iteration }}">
                                    <img src="{{ asset('storage/' . $foto) }}"
                                        alt="Foto {{ $loop->iteration }} {{ $barang->nama_barang }}" class="w-100 h-100"
                                        style="object-fit: cover;">
                                </button>
                            @endforeach
                        </div>
                    @endif
                    <div class="row g-3 mt-3">
                        <div class="summary-box">
                            <div class="form-label-pink">Deskripsi</div>
                            <p class="mb-0">{{ $barang->deskripsi }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="mb-2">{{ $barang->nama_barang }}</h2>
                    @if ($hasUkuran && $barang->ukurans->pluck('harga_ukuran')->filter()->isNotEmpty())
                        @php
                            $h = $barang->ukurans
                                ->pluck('harga_ukuran')
                                ->filter()
                                ->map(fn($p) => (float) $p)
                                ->sort()
                                ->values();
                            $min = $h->first();
                            $max = $h->last();
                        @endphp
                        <h3 class="text-pink mb-4" id="price-display">
                            Rp {{ number_format($min, 0, ',', '.') }}
                            @if ($max > $min)
                                - Rp {{ number_format($max, 0, ',', '.') }}
                            @endif
                        </h3>
                    @else
                        <h3 class="text-pink mb-4" id="price-display">Rp {{ number_format($barang->harga, 0, ',', '.') }}
                        </h3>
                    @endif
                    <div class="summary-box mb-4">
                        {{-- @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif --}}

                        @if (!auth('customer')->check())
                            <a href="{{ route('login') }}" class="btn btn-pink w-100">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Login untuk Masukkan Keranjang
                            </a>
                        @elseif ($stokReady < 1)
                            <button type="button" class="btn btn-secondary w-100" disabled>
                                Stok Tidak Tersedia
                            </button>
                        @else
                            <form method="POST" action="{{ route('keranjang.store') }}">
                                @csrf
                                <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">

                                @if ($hasUkuran)
                                    <div class="mb-3">
                                        <label for="id_ukuran" class="form-label-pink">Ukuran</label>
                                        <select id="id_ukuran" name="id_ukuran"
                                            class="form-select @error('id_ukuran') is-invalid @enderror" required>
                                            <option value="">Pilih Ukuran</option>
                                            @foreach ($barang->ukurans as $ukuran)
                                                @if ($ukuran->stok_ukuran > 0)
                                                    <option value="{{ $ukuran->id_ukuran }}" @selected(old('id_ukuran') == $ukuran->id_ukuran)
                                                        @if (!is_null($ukuran->harga_ukuran)) data-harga="{{ $ukuran->harga_ukuran }}" @endif>
                                                        {{ $ukuran->nama_ukuran }}
                                                        @if ($ukuran->ukuran)
                                                            - {{ $ukuran->ukuran }}
                                                        @endif
                                                        (Stok {{ $ukuran->stok_ukuran }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('id_ukuran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="jumlah_barang" class="form-label-pink">Jumlah</label>
                                    <input id="jumlah_barang" type="number" name="jumlah_barang" min="1"
                                        @if (!$hasUkuran) max="{{ $stokReady }}" @endif
                                        value="{{ old('jumlah_barang', 1) }}"
                                        class="form-control @error('jumlah_barang') is-invalid @enderror" required>
                                    @error('jumlah_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-pink w-100">
                                    <i class="fa-solid fa-cart-plus"></i> Masukkan Keranjang
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="summary-box h-100">
                                <div class="form-label-pink">Brand</div>
                                <div>{{ $barang->brand?->nama_brand ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="summary-box h-100">
                                <div class="form-label-pink">Kategori</div>
                                <div>{{ $barang->kategori?->nama_kategori ?? '-' }}</div>
                            </div>
                        </div>
                        @if ($hasUkuran)
                            <div class="col-sm-6">
                                <div class="summary-box h-100">
                                    <div class="form-label-pink">Stok Total</div>
                                    <div>{{ $stokReady }}</div>
                                </div>
                            </div>
                        @else
                            <div class="col-sm-6">
                                <div class="summary-box h-100">
                                    <div class="form-label-pink">Stok</div>
                                    <div>{{ $stokReady }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <div class="summary-box h-100">
                                <div class="form-label-pink">Berat</div>
                                <div>{{ $formattedBerat }}</div>
                            </div>
                        </div>
                    </div>
                    @if ($hasUkuran)
                        <div class="summary-box mb-3">
                            <div class="form-label-pink">Pilihan Ukuran</div>
                            <div class="mt-3">
                                @foreach ($barang->ukurans as $ukuran)
                                    <dl class="row mb-2">
                                        <dt class="col-sm-4">{{ $ukuran->nama_ukuran }}</dt>
                                        <dd class="col-sm-4 text-muted mb-0">{{ $ukuran->ukuran }}</dd>
                                        <dd class="col-sm-4 text-muted mb-0">{{ $ukuran->stok_ukuran }}</dd>
                                    </dl>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($barang->preorder === 'Tersedia')
                        <div class="summary-box">
                            <div class="form-label-pink">Preorder</div>
                            <div>
                                {{ $barang->preorder }}
                                @if ($barang->preorder === 'Tersedia' && $barang->estimasi_preorder)
                                    <span class="text-muted">· Estimasi {{ $barang->estimasi_preorder }} hari</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
