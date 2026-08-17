@extends('pegawai.layouts.app')

@section('title', 'Tambah Brand - Toko XYZ')

@php
    $activ = 'brand';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.abrand') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="auth-title">Tambah Brand</div>
                
                <div class="mb-2">
                    <label for="logo" class="form-label-pink">Logo Brand</label>
                    <input id='logo' name="logo" type="file" class="form-control form-control-pink" required />
                    @error('logo')
                        <label for="logo" class="form-label-pink text-danger mt-2">
                            {{ $message }}
                        </label>
                    @else
                        <p class="form-label-rememberme mt-2 formhint">
                            Logo harus memiliki ekstensi JPG, PNG, JPEG.
                            <br>
                            Ukuran file logo maksimal 5MB.
                        </p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="nama_brand" class="form-label-pink">Nama Brand</label>
                    <input id='nama_brand' name="nama_brand" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_brand') }}" placeholder="Nama Brand" required />
                    @error('nama_brand')
                        <label for="nama_brand" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Tambah Brand
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.kbrand') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

