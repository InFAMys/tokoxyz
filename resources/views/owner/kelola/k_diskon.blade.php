@extends('owner.layouts.app')

@section('title', 'Kelola Diskon - Toko XYZ')

@php
    $activ = 'diskon';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Kelola Diskon</h1>
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <a href="{{ route('owner.adddiskon') }}" class="btn btn-pink">
                        <i class="fa-solid fa-plus"></i> Tambah Diskon
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
                            class="form-control form-control-pink live-search" data-tbody="rows-diskon"
                            placeholder="Cari Diskon..." />
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-pink table-borderless text-center mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Diskon</th>
                            <th>Kode Diskon</th>
                            <th>Jumlah Diskon</th>
                            <th>Mulai-Akhir Diskon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows-diskon">
                        @include('owner.kelola._diskon_rows')
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

