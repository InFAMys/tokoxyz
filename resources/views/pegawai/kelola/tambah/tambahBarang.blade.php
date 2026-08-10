@extends('pegawai.layouts.app')

@section('title', 'Tambah Barang - Toko XYZ')

@php
    $activ = 'barang';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('pegawai.addbarang') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="auth-title">Tambah Barang</div>
                {{-- @if (session('astatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('astatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-2">
                    <label for="id_brand" class="form-label-pink">Brand</label>
                    <select id="id_brand" name="id_brand" class="form-select form-control-pink" required>
                        <option value="">Pilih brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id_brand }}" @selected(old('id_brand') == $brand->id_brand)>{{ $brand->nama_brand }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_brand')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="id_kategori" class="form-label-pink">Kategori</label>
                    <select id="id_kategori" name="id_kategori" class="form-select form-control-pink" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id_kategori }}" @selected(old('id_kategori') == $kategori->id_kategori)>
                                {{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('id_kategori')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="kode_barang" class="form-label-pink">Kode Barang</label>
                    <input id="kode_barang" name="kode_barang" type="text" maxlength="15"
                        class="form-control form-control-pink" value="{{ old('kode_barang') }}" required />
                    @error('kode_barang')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="nama_barang" class="form-label-pink">Nama Barang</label>
                    <input id="nama_barang" name="nama_barang" type="text" maxlength="32"
                        class="form-control form-control-pink" value="{{ old('nama_barang') }}" required />
                    @error('nama_barang')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="deskripsi" class="form-label-pink">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control form-control-pink" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="thumbnail" class="form-label-pink">Thumbnail Barang</label>
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png"
                        class="form-control form-control-pink" required />
                    @error('thumbnail')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="foto" class="form-label-pink">Foto Barang (maksimal 5)</label>
                    <input id="foto" name="foto[]" type="file" accept="image/jpeg,image/png"
                        class="form-control form-control-pink" multiple required />
                    @error('foto')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                    @error('foto.*')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="harga" class="form-label-pink">Harga</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="harga-addon1">Rp</span>
                        <input type="text" inputmode="numeric" autocomplete="off" class="form-control form-control-pink"
                            name="harga" placeholder="Harga"
                            aria-label="Harga" aria-describedby="harga-addon1"
                            value="{{ old('harga') }}" required>
                    </div>
                    @error('harga')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="berat" class="form-label-pink">Berat</label>
                    <div class="input-group mb-3">
                        <input id="berat" type="text" class="form-control form-control-pink" name="berat"
                            placeholder="0,5" aria-label="Berat" aria-describedby="berat-addon1" inputmode="decimal"
                            value="{{ str_replace('.', ',', old('berat', '')) }}">
                        <span class="input-group-text" id="berat-addon1">kg</span>
                    </div>
                    @error('berat')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Tambah Barang
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.barang') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

