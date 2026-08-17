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

                    <form method="GET" class="ms-auto search-wrapper filter-search">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-pink live-search" data-target="rows-barang"
                            placeholder="Cari barang..." />
                    </form>
                </div>
                <div id="rows-barang">
                    @include('pegawai.kelola._barang_rows')
                </div>
            </div>
        </div>
    </div>
@endsection
