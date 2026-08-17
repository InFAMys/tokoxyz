@extends('pegawai.layouts.app')

@section('title', ' Brand - Toko XYZ')

@php
    $activ = 'brand';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Kelola Brand</h1>
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <a href="{{ route('pegawai.abrand') }}" class="btn btn-pink">
                        <i class="fa-solid fa-plus"></i> Tambah Brand
                    </a>

                    <form method="GET" class="ms-auto search-wrapper filter-search">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-pink live-search" data-target="rows-brand"
                            placeholder="Cari Brand..." />
                    </form>
                </div>
                <div id="rows-brand">
                    @include('pegawai.kelola._brand_rows')
                </div>
            </div>
        </div>
    </div>
@endsection
