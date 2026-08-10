@extends('pegawai.layouts.app')

@section('title', ' Barang - Toko XYZ')

@php
    $activ = 'barang';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Kelola Barang</h1>
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <a href="{{ route('pegawai.abarang') }}" class="btn btn-pink">
                        <i class="fa-solid fa-plus"></i> Tambah Barang
                    </a>
                    {{-- @if (session('delStatus'))
                        <div class="alert alert-success alert-dismissible fade show mx-auto" role="alert">
                            {{ session('delStatus') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif --}}
                    <form method="GET" class="ms-auto search-wrapper" style="width: 230px">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-pink live-search" data-tbody="rows-barang"
                            placeholder="Cari barang..." />
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-pink table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Thumbnail</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Berat</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rows-barang">
                            @include('pegawai.kelola._barang_rows')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
