@extends('pegawai.layouts.app')

@php
    $activ = 'barang';
    $brg = $stokbrg;
@endphp

@section('title', 'Tambah Varian: ' . $brg->nama_barang . '- ' . $namaToko)


@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.addukuran', $brg->id_barang) }}" method="post" enctype="multipart/form-data"
                data-confirm="Tambah Varian?">
                @csrf
                <div class="auth-titleA mb-3">Tambah Varian</div>
                <div class="auth-subtitleA">{{ $brg->nama_barang }}</div>

                <div class="mb-2">
                    <label for="nama_varian" class="form-label-pink">Nama Varian</label>
                    <input id='nama_varian' name="nama_varian" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_varian') }}" placeholder="M - Biru" required autofocus />
                    @error('nama_varian')
                        <label for="nama_varian" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="varian" class="form-label-pink">Deskripsi Varian</label>
                    <input id='varian' name="varian" type="text" class="form-control form-control-pink"
                        value="{{ old('varian') }}" placeholder="Ukuran M - Warna Biru" required autofocus />
                    @error('varian')
                        <label for="varian" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="harga_varian" class="form-label-pink">Harga Varian</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="harga_varian-addon1">Rp</span>
                        <input type="text" inputmode="numeric" autocomplete="off" class="form-control form-control-pink"
                            name="harga_varian" placeholder="Harga Ukuran" aria-label="Harga Ukuran"
                            aria-describedby="harga_varian-addon1" value="{{ old('harga_varian') }}" required>
                    </div>
                    @error('harga_varian')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Tambah Ukuran
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.ukuran', $brg->id_barang) }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection
