@extends('pegawai.layouts.app')

@php
    $activ = 'barang';
    $brg = $stokbrg;
@endphp

@section('title', 'Tambah Ukuran: ' . $brg->nama_barang . '- Toko XYZ')


@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.addukuran', $brg->id_barang) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="auth-titleA mb-3">Tambah Ukuran</div>
                <div class="auth-subtitleA">{{ $brg->nama_barang }}</div>
                {{--
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('astatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-2">
                    <label for="nama_ukuran" class="form-label-pink">Nama Ukuran</label>
                    <input id='nama_ukuran' name="nama_ukuran" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_ukuran') }}" placeholder="Nama Ukuran" required autofocus />
                    @error('nama_ukuran')
                        <label for="nama_ukuran" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="ukuran" class="form-label-pink">Ukuran</label>
                    <input id='ukuran' name="ukuran" type="text" class="form-control form-control-pink"
                        value="{{ old('ukuran') }}" placeholder="Ukuran" required autofocus />
                    @error('ukuran')
                        <label for="ukuran" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
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

