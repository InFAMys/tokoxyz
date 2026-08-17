@extends('pegawai.layouts.app')

@section('title', 'Kelola Stok: ' . $stokbrg->nama_barang . ' - Toko XYZ')
@php
    $activ = 'barang';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <a class="btn btn-pink-outline" href="{{ route('pegawai.detailbarang', $stokbrg->id_barang) }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title my-4">Kelola Stok Ukuran Barang: {{ $stokbrg->nama_barang }}</h1>
            <div class="card-pink p-3">

                <div class="table-responsive mobile-card-responsive">
                    <table class="table table-pink table-borderless mobile-card-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">Nama Ukuran</th>
                            <th>Ukuran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stok as $sk)
                            <tr>
                                <td data-label="Nama Ukuran" class="text-center">{{ $sk->nama_ukuran }}</td>
                                <td data-label="Ukuran">{{ $sk->ukuran }}</td>
                                <td data-label="Aksi" class="mobile-card-actions mobile-card-form">
                                    <form action="{{ route('pegawai.ustoku', [$stokbrg->id_barang, $sk->id_ukuran]) }}"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-4">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-5">

                                                <div class="input-group flex-grow-1 stock-input-group">
                                                    <button type="button" class="btn btn-pink-outline qty-adjust-btn"
                                                        data-qty-delta="-1" aria-label="Kurangi stok">
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                    <input id="stok" name="stok" type="number" min="0"
                                                        step="1" class="form-control form-control-pink text-center"
                                                        value="{{ old('stok_ukuran', $sk->stok_ukuran) }}"
                                                        placeholder="Stok" required />
                                                    <button type="button" class="btn btn-pink-outline qty-adjust-btn"
                                                        data-qty-delta="1" aria-label="Tambah stok">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                                <button type="submit" class="btn btn-pink w-md-auto mb-0">
                                                    <i class="fa-solid fa-plus"></i> Ubah Stok
                                                </button>
                                                <div class="w-md-auto mb-0">

                                                </div>
                                            </div>
                                            @error('stok')
                                                <label for="stok" class="form-label-pink text-danger">
                                                    {{ $message }}
                                                </label>
                                            @enderror

                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
