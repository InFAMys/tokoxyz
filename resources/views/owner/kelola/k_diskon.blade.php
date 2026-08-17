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

                    <form method="GET" class="ms-auto search-wrapper filter-search">
                        <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-pink live-search" data-target="rows-diskon"
                            placeholder="Cari Diskon..." />
                    </form>
                </div>
                <div id="rows-diskon">
                    @include('owner.kelola._diskon_rows')
                </div>
            </div>
        </div>
    </div>
@endsection
