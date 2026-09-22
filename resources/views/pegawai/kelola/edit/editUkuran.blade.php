@extends('pegawai.layouts.app')

@section('title', 'Edit Varian - Toko XYZ')
@php
    $activ = 'ukuran';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.uukuran', [$ukuran->id_barang, $ukuran->id_ukuran]) }}" method="post"
                enctype="multipart/form-data" data-confirm="Perbarui Varian?">
                @csrf
                @method('PUT')
                <div class="auth-title">Edit Varian</div>

                <div class="mb-2">
                    <label for="nama_ukuran" class="form-label-pink">Nama Varian</label>
                    <input id='nama_ukuran' name="nama_ukuran" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_ukuran', $ukuran->nama_ukuran) }}" placeholder="M - Biru" required autofocus />
                    @error('nama_ukuran')
                        <label for="nama_ukuran" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="ukuran" class="form-label-pink">Deskripsi Varian</label>
                    <input id='ukuran' name="ukuran" type="text" class="form-control form-control-pink"
                        value="{{ old('ukuran', $ukuran->ukuran) }}" placeholder="Ukuran M - Warna Biru" required
                        autofocus />
                    @error('ukuran')
                        <label for="ukuran" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Ubah Varian
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.ukuran', $ukuran->id_barang) }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection
