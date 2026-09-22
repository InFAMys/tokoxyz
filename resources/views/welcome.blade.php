@extends('customer.layouts.app')

@section('title', 'Toko XYZ')
@php
    $activ = 'home';
    $namaToko = $pengaturan['nama_toko'] ?? 'Toko XYZ';
    $tagline = $pengaturan['tagline'] ?? 'Temukan produk terbaik dengan harga terjangkau';
    $deskripsi = $pengaturan['deskripsi'] ?? '';
@endphp

@section('content')
    <div class="main-content">
        <form method="GET" action="{{ route('barang.search') }}" class="d-flex mx-auto mb-4 hero-search"
            role="search">
            <input class="form-control hero-search-input" type="search" name="q" placeholder="Cari produk kesukaanmu..."
                aria-label="Cari produk">
            <button class="btn btn-pink hero-search-btn" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
        </form>

        <!-- Produk Terbaru Carousel -->
        @php
            $slides = $barangNew
                ->filter(fn ($b) => $b->stokReady() >= 1 || $b->preorder === 'Tersedia')
                ->take(6);
        @endphp
        @if ($slides->isNotEmpty())
            <div id="produkCarousel" class="carousel slide carousel-fade mb-4 position-relative"
                data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner rounded-4 overflow-hidden shadow-sm">
                    @foreach ($slides as $i => $sl)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <a href="{{ route('barang.detail', $sl->id_barang) }}" class="text-decoration-none">
                                <div class="hero-banner mb-0 carousel-banner d-flex align-items-center"
                                    style="border-radius:0">
                                    <div class="row g-0 w-100 align-items-center">
                                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                                            <img src="{{ asset('storage/' . $sl->thumbnailPath()) }}"
                                                alt="{{ $sl->nama_barang }}"
                                                class="img-fluid rounded-3 shadow"
                                                style="max-height:280px;object-fit:cover" />
                                        </div>
                                        <div class="col-12 col-md-6 ps-md-5">
                                            <span class="badge text-bg-light text-pink mb-2">Produk Terbaru</span>
                                            @if ($sl->preorder === 'Tersedia' && $sl->stokReady() < 1)
                                                <span class="badge text-bg-warning mb-2">Preorder</span>
                                            @endif
                                            <h2 class="fw-bold mb-2">{{ $sl->nama_barang }}</h2>
                                            <div class="fs-5 fs-md-4 fw-semibold mb-3">
                                                @include('customer.partials.harga', ['item' => $sl])
                                            </div>
                                            <p class="d-none d-md-block mb-4" style="opacity:0.9">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($sl->deskripsi), 120) }}
                                            </p>
                                            <span class="btn btn-light"
                                                style="color:var(--pink-600);font-weight:700;border-radius:25px;padding:0.5rem 1.5rem">
                                                Beli Sekarang <i class="fa-solid fa-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center column-gap-2 mt-3 position-absolute"
                    style="bottom:12px;right:12px;z-index:5">
                    <button class="btn btn-light rounded-circle shadow-sm carousel-nav" type="button"
                        data-bs-target="#produkCarousel" data-bs-slide="prev" aria-label="Sebelumnya">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-light rounded-circle shadow-sm carousel-nav" type="button"
                        data-bs-target="#produkCarousel" data-bs-slide="next" aria-label="Berikutnya">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        @else
            <div class="hero-banner text-center">
                <span class="badge text-bg-light text-pink mb-2" style="font-size:0.8em">✨ Online Store Terpercaya</span>
                <h2 class="fw-bold">{{ $namaToko }} <i class="fa-solid fa-shop"></i></h2>
                <p class="mb-4" style="opacity: 0.9; max-width: 520px; margin-inline:auto">
                    {{ $tagline }}
                </p>
                <a href="#produk-baru" class="btn"
                    style="background:#fff;color:var(--pink-600);font-weight:700;border-radius:25px;padding:0.5rem 1.5rem;">
                    Belanja Sekarang <i class="fa-solid fa-arrow-down"></i>
                </a>
            </div>
        @endif

        <!-- Keunggulan -->
        <div class="row g-3 mb-4">
            @if ($kategoris->isNotEmpty())
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <h6 class="fw-bold text-pink mb-3">
                                Kategori
                                <span class="text-muted fw-normal small">({{ $kategoris->count() }})</span>
                            </h6>
                            <div class="list-group list-group-flush kategori-scroll">
                                @foreach ($kategoris as $kat)
                                    <a href="{{ route('kategori', $kat->id_kategori) }}"
                                        class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0 py-2">
                                        <span class="kategori-dot me-2"></span>
                                        <span class="fw-semibold">{{ $kat->nama_kategori }}</span>
                                        <i class="fa-solid fa-chevron-right ms-auto" style="color:var(--pink-600)"></i>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('kategori.all') }}"
                                class="btn btn-pink-outline btn-sm w-100 mt-3">Lihat Semua Kategori</a>
                        </div>
                    </div>
                </div>
            @endif
            @if ($brands->isNotEmpty())
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <h6 class="fw-bold text-pink mb-3">
                                Brand
                                <span class="text-muted fw-normal small">({{ $brands->count() }})</span>
                            </h6>
                            <div class="list-group list-group-flush kategori-scroll">
                                @foreach ($brands as $brand)
                                    <a href="{{ route('brand', $brand->id_brand) }}"
                                        class="list-group-item list-group-item-action d-flex align-items-center border-0 px-0 py-2">
                                        @if ($brand->logo)
                                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->nama_brand }}"
                                                class="rounded-circle me-2" style="width:28px;height:28px;object-fit:cover">
                                        @else
                                            <span class="kategori-dot me-2"></span>
                                        @endif
                                        <span class="fw-semibold">{{ $brand->nama_brand }}</span>
                                        <i class="fa-solid fa-chevron-right ms-auto" style="color:var(--pink-600)"></i>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('brand.all') }}"
                                class="btn btn-pink-outline btn-sm w-100 mt-3">Lihat Semua Brand</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Produk Baru -->
        <h5 class="fw-bold text-pink mb-3" id="produk-baru">Produk Baru</h5>
        <div class="row g-3 mb-4">
            @foreach ($barangNew as $bn)
                <a href="{{ route('barang.detail', $bn->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                    <div class="product-card">
                        <div class="product-img position-relative">
                            <img class="img-fluid" src="{{ asset('storage/' . $bn->thumbnailPath()) }}"
                                alt="{{ $bn->nama_barang }}" />
                            @if ($bn->stokReady() < 1)
                                @if ($bn->preorder === 'Tersedia')
                                    <span class="position-absolute top-0 start-0 badge text-bg-warning" style="font-size:0.65em">Preorder</span>
                                @else
                                    <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                        style="font-size:0.65em;opacity:.85">Habis</span>
                                @endif
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="product-name">{{ $bn->nama_barang }}</div>
                            <div class="product-price">
                                @include('customer.partials.harga', ['item' => $bn])
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Paling Banyak Dibeli -->
        @if ($barangTerlaris->isNotEmpty())
            <h5 class="fw-bold text-pink mb-3">Paling Banyak Dibeli</h5>
            <div class="row g-3 mb-4">
                @foreach ($barangTerlaris as $bt)
                    <a href="{{ route('barang.detail', $bt->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                        <div class="product-card">
                            <div class="product-img position-relative">
                                <img class="img-fluid" src="{{ asset('storage/' . $bt->thumbnailPath()) }}"
                                    alt="{{ $bt->nama_barang }}" />
                                @if ($bt->stokReady() < 1)
                                    @if ($bt->preorder === 'Tersedia')
                                        <span class="position-absolute top-0 start-0 badge text-bg-warning" style="font-size:0.65em">Preorder</span>
                                    @else
                                        <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                            style="font-size:0.65em;opacity:.85">Habis</span>
                                    @endif
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="product-name">{{ $bt->nama_barang }}</div>
                                <div class="product-price">
                                    @include('customer.partials.harga', ['item' => $bt])
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Rekomendasi -->
        <h5 class="fw-bold text-pink mb-3">Rekomendasi Untukmu</h5>
        <div class="row g-3 mb-4">
            @foreach ($barangRand as $br)
                <a href="{{ route('barang.detail', $br->id_barang) }}" class="col-6 col-md-3 col-lg-2 text-decoration-none">
                    <div class="product-card">
                        <div class="product-img position-relative">
                            <img class="img-fluid" src="{{ asset('storage/' . $br->thumbnailPath()) }}"
                                alt="{{ $br->nama_barang }}" />
                            @if ($br->stokReady() < 1)
                                @if ($br->preorder === 'Tersedia')
                                    <span class="position-absolute top-0 start-0 badge text-bg-warning" style="font-size:0.65em">Preorder</span>
                                @else
                                    <span class="position-absolute top-0 start-0 badge text-bg-secondary"
                                        style="font-size:0.65em;opacity:.85">Habis</span>
                                @endif
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="product-name">{{ $br->nama_barang }}</div>
                            <div class="product-price">
                                @include('customer.partials.harga', ['item' => $br])
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Tentang Kami / Profil Perusahaan -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="row g-0 align-items-stretch">
                <div class="col-md-5 hero-banner d-flex align-items-center justify-content-center"
                    style="border-radius:0;margin-bottom:0">
                    <div class="text-center">
                        <i class="fa-solid fa-shop fs-1 mb-2"></i>
                        <h4 class="fw-bold mb-1">{{ $namaToko }}</h4>
                        <p class="small mb-0" style="opacity:0.85">{{ $tagline }}</p>
                    </div>
                </div>
                <div class="col-md-7 p-4 p-md-5">
                    <h5 class="fw-bold text-pink mb-3">Tentang Kami</h5>
                    @if ($deskripsi)
                        <p class="text-muted">{{ $deskripsi }}</p>
                    @endif
                    <ul class="list-unstyled mb-0">
                        @if (!empty($pengaturan['alamat']))
                            <li class="mb-2"><i class="fa-solid fa-location-dot text-pink me-2"></i>{{ $pengaturan['alamat'] }}</li>
                        @endif
                        @if (!empty($pengaturan['jam_buka']))
                            <li class="mb-2"><i class="fa-solid fa-clock text-pink me-2"></i>
                                @if (!empty($pengaturan['jam_tutup']))
                                    @if (!empty($pengaturan['jam_hari']))
                                        {{ $pengaturan['jam_hari'] }},
                                    @endif
                                    {{ $pengaturan['jam_buka'] }} - {{ $pengaturan['jam_tutup'] }} WIB
                                @else
                                    {{ $pengaturan['jam_buka'] }}
                                @endif
                            </li>
                        @endif
                        @if (!empty($pengaturan['no_telp']))
                            <li class="mb-2"><i class="fa-solid fa-phone text-pink me-2"></i>{{ $pengaturan['no_telp'] }}</li>
                        @endif
                        @if (!empty($pengaturan['email']))
                            <li class="mb-2"><i class="fa-solid fa-envelope text-pink me-2"></i>{{ $pengaturan['email'] }}</li>
                        @endif
                    </ul>
                    @php $sosmed = array_filter([
                        'instagram' => $pengaturan['instagram'] ?? '',
                        'facebook' => $pengaturan['facebook'] ?? '',
                        'tiktok' => $pengaturan['tiktok'] ?? '',
                    ]); @endphp
                    @if ($sosmed)
                        <div class="mt-4 d-flex gap-2">
                            @foreach ($sosmed as $key => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    class="btn btn-sm btn-pink-outline rounded-pill">
                                    <i class="fa-brands fa-{{ $key }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
