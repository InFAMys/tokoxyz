@extends('customer.layouts.app')

@section('title', 'Semua Kategori - Toko XYZ')

@php
    $activ = 'home';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="page-title mb-0">Semua Kategori</h1>
                <p class="text-muted mb-0">{{ $kategoris->count() }} kategori tersedia</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-pink-outline align-self-start align-self-md-center">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($kategoris->isEmpty())
            <div class="card-pink p-4 text-center">
                <p class="mb-0">Belum ada kategori.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach ($kategoris as $kat)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('kategori', $kat->id_kategori) }}"
                            class="card border-0 shadow-sm text-decoration-none h-100">
                            <div class="card-body d-flex align-items-center">
                                <span class="kategori-dot me-2"></span>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $kat->nama_kategori }}</div>
                                    <div class="text-muted small">{{ $kat->jumlah_barang }} produk</div>
                                </div>
                                <i class="fa-solid fa-chevron-right ms-auto" style="color:var(--pink-600)"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
