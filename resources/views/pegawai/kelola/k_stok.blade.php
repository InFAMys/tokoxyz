@extends('pegawai.layouts.app')

@section('title', 'Kelola Stok: ' . $stokbrg->nama_barang . ' - Toko XYZ')
@php
    $activ = 'barang';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.ustok', $stokbrg->id_barang) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="auth-titleA">Kelola Stok</div>
                <div class="auth-subtitleA mt-2">{{ $stokbrg->nama_barang }}</div>
                {{-- @if (session('estatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('estatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-4">
                    <label for="stok" class="form-label-pink">Stok</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-pink-outline qty-adjust-btn" data-qty-delta="-1"
                            aria-label="Kurangi stok">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <input id="stok" name="stok" type="number" min="0" step="1"
                            class="form-control form-control-pink text-center" value="{{ old('stok', $stokbrg->stok) }}"
                            placeholder="Stok" required />
                        <button type="button" class="btn btn-pink-outline qty-adjust-btn" data-qty-delta="1"
                            aria-label="Tambah stok">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    @error('stok')
                        <label for="stok" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Ubah Stok
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.detailbarang', $stokbrg->id_barang) }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

