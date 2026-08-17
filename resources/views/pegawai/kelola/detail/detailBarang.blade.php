@extends('pegawai.layouts.app')

@section('title', $barang->nama_barang . ' - Detail Barang - Toko XYZ')

@php
    $activ = 'barang';
    $galleryImages = collect([$barang->thumbnail])
        ->merge($barang->foto ?? [])
        ->filter()
        ->unique()
        ->values();
    $mainImage = $galleryImages->first();
    $hasUkuran = $stok != null;
    $stokReady = $hasUkuran ? $stok->sum('stok_ukuran') : $barang->stok;
    $formattedBerat = !is_null($barang->berat)
        ? rtrim(rtrim(number_format((float) $barang->berat, 1, ',', '.'), '0'), ',') . ' kg'
        : '-';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <p class="text-pink fw-semibold mb-1">{{ $barang->kode_barang }}</p>
                <h1 class="page-title mb-0">{{ $barang->nama_barang }}</h1>
            </div>
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a href="{{ route('pegawai.barang') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('pegawai.stok', $barang->id_barang) }}" class="btn btn-green">
                    <i class="fa-solid fa-boxes-stacked"></i> Stok
                </a>
                <a href="{{ route('pegawai.ukuran', $barang->id_barang) }}" class="btn btn-detail">
                    <i class="fa-solid fa-ruler"></i> Ukuran
                </a>
                <a href="{{ route('pegawai.ebarang', $barang->id_barang) }}" class="btn btn-edit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
                <button type="button" class="btn btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
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
                        <div class="summary-box mb-4">
                            <div class="form-label-pink">Deskripsi</div>
                            <p class="mb-0">{{ $barang->deskripsi }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h2 class="mb-0">{{ $barang->nama_barang }}</h2>
                        @if ($barang->status === 'Ditampilkan')
                            <span class="badge rounded-pill text-bg-success">{{ $barang->status }}</span>
                        @else
                            <span class="badge rounded-pill text-bg-warning">{{ $barang->status }}</span>
                        @endif
                    </div>

                    @if ($hasUkuran && $stok->pluck('harga_ukuran')->filter()->isNotEmpty())
                        @php
                            $h = $stok->pluck('harga_ukuran')->filter()->map(fn($p) => (float) $p)->sort()->values();
                            $min = $h->first();
                            $max = $h->last();
                        @endphp
                        <h3 class="text-pink mb-4">
                            Rp {{ number_format($min, 0, ',', '.') }}
                            @if ($max > $min)
                                - Rp {{ number_format($max, 0, ',', '.') }}
                            @endif
                        </h3>
                    @else
                        <h3 class="text-pink mb-4">Rp {{ number_format($barang->harga, 0, ',', '.') }}</h3>
                    @endif

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
                            <div class="form-label-pink">Ukuran</div>
                            <div class="mt-3">
                                @foreach ($stok as $uk)
                                    <dl class="row mb-2">
                                        <dt class="col-sm-4">{{ $uk->nama_ukuran }}</dt>
                                        <dd class="col-sm-4 text-muted mb-0">{{ $uk->ukuran }}</dd>
                                        <dd class="col-sm-4 text-muted mb-0">
                                            @if (!is_null($uk->harga_ukuran))
                                                Rp {{ number_format($uk->harga_ukuran, 0, ',', '.') }}
                                            @else
                                                (Stok {{ $uk->stok_ukuran }})
                                            @endif
                                        </dd>
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
                                @if ($barang->estimasi_preorder)
                                    <span class="text-muted">· Estimasi {{ $barang->estimasi_preorder }} hari</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-pink">
                <div class="modal-header">
                    <h1 class="modal-title fs-4" id="deleteModalLabel">Hapus Barang?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center my-4">
                    Hapus barang {{ $barang->nama_barang }}?
                </div>
                <div class="modal-footer mx-auto">
                    <form action="{{ route('pegawai.delbarang', $barang->id_barang) }}" method="post">
                        @csrf
                        <button class="btn btn-delete btn-sm" type="submit">
                            <i class="fa-solid fa-trash"></i> HAPUS
                        </button>
                    </form>
                    <button class="btn btn-green btn-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> BATAL
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
