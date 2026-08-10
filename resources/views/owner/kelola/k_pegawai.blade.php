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
                {{-- @if (session('delStatus'))
                    <div class="alert alert-success alert-dismissible fade show mx-auto" role="alert">
                        {{ session('delStatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <form method="GET" class="ms-auto search-wrapper" style="width: 230px">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="form-control form-control-pink live-search" data-tbody="rows-pegawai"
                        placeholder="Search..." />
                </form>
            </div>
                <div class="table-responsive">
                    <table class="table table-pink table-borderless mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pegawai</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                    <tbody id="rows-pegawai">
                        @include('owner.kelola._pegawai_rows')
                    </tbody>
                    </table>
                </div>
        </div>
    </div>
@endsection

