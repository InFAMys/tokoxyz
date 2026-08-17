@extends('pegawai.layouts.app')

@section('title', 'Edit Kategori - Toko XYZ')
@php
    $activ = 'kategori';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.ukategori', $kategori->id_kategori) }}" method="post"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="auth-title">Edit Kategori</div>
                
                <div class="mb-4">
                    <label for="nama_kategori" class="form-label-pink">Nama Kategori</label>
                    <input id='nama_kategori' name="nama_kategori" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_kategori', $kategori->nama_kategori) }}" placeholder="Nama Kategori" required />
                    @error('nama_kategori')
                        <label for="nama_kategori" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Ubah Kategori
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.kategori') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

