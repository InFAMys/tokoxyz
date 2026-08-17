@extends('owner.layouts.app')

@section('title', 'Kelola Pegawai - Toko XYZ')
@php
    $activ = 'pegawai';
@endphp

@section('content')
    <div class="main-content">
        <h1 class="page-title">Kelola Pegawai</h1>
        <div class="card-pink p-3">
            <div class="filter-bar">
                <a class="btn btn-pink" href="{{ route('owner.addpegawai') }}">
                    <i class="fa-solid fa-plus"></i> Tambah Akun Pegawai
                </a>

                <form method="GET" class="ms-auto search-wrapper filter-search">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="form-control form-control-pink live-search" data-target="rows-pegawai"
                        placeholder="Search..." />
                </form>
            </div>
                <div id="rows-pegawai">
                    @include('owner.kelola._pegawai_rows')
                </div>
        </div>
    </div>
@endsection
