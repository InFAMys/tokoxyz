@extends('pegawai.layouts.app')

@section('title', 'Kategori - Toko XYZ')

@php
    $activ = 'kategori';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Kelola Kategori</h1>
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <a href="{{ route('pegawai.akategori') }}" class="btn btn-pink">
                        <i class="fa-solid fa-plus"></i> Tambah Kategori
                    </a>

                    <form method="GET" class="ms-auto search-wrapper filter-search">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-pink live-search" data-target="rows-kategori"
                            placeholder="Cari Kategori..." />
                    </form>
                </div>
                <div id="rows-kategori">
                    @include('pegawai.kelola._kategori_rows')
                </div>
            </div>
        </div>
    </div>
@endsection
