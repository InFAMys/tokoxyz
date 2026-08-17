@extends('pegawai.layouts.app')

@section('title', 'Ukuran - Toko XYZ')

@php
    $activ = 'barang';
    $brg = $stokbrg;
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Ukuran {{ $brg->nama_barang }}</h1>
            {{-- <div class="d-flex gap-2"> --}}
            <a href="{{ route('pegawai.detailbarang', $brg->id_barang) }}" class="btn btn-pink-outline mb-3">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            {{-- </div> --}}
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <a href="{{ route('pegawai.addukuran', $brg->id_barang) }}" class="btn btn-pink">
                        <i class="fa-solid fa-plus"></i> Tambah Ukuran
                    </a>


                </div>
                <div class="table-responsive mobile-card-responsive">
                    <table class="table table-pink table-borderless mobile-card-table mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Ukuran</th>
                            <th>Ukuran</th>
                            <th>Harga (Rp)</th>
                            <th>Stok Ukuran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($stok == null)
                            {{-- <h1>Tidak Ada Ukuran</h1> --}}
                            <tr>
                                <td colspan="5" class="text-center">
                                    <h1 class="my-5 fs-3">Tidak Ada Ukuran</h1>
                                </td>
                            </tr>
                        @else
                            @foreach ($stok as $uk)
                                <tr>
                                    <td data-label="No">{{ $loop->iteration }}</td>
                                    <td data-label="Nama Ukuran">{{ $uk->nama_ukuran }}</td>
                                    <td data-label="Ukuran">{{ $uk->ukuran }}</td>
                                    <td data-label="Harga" class="mobile-card-form">
                                        <form action="{{ route('pegawai.uhargau', [$uk->id_barang, $uk->id_ukuran]) }}"
                                            method="post">
                                            @csrf
                                            @method('PUT')
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" inputmode="numeric" autocomplete="off"
                                                    name="harga_ukuran"
                                                    value="{{ old('harga_ukuran', $uk->harga_ukuran) }}"
                                                    class="form-control form-control-pink price-input-compact"
                                                    required>
                                                <button type="submit" class="btn btn-pink">
                                                    <i class="fa-solid fa-floppy-disk"></i>
                                                </button>
                                            </div>
                                        </form>
                                        {{-- @if (session('ehargastatus-' . $uk->id_ukuran))
                                            <small class="text-success">{{ session('ehargastatus-' . $uk->id_ukuran) }}</small>
                                        @endif --}}
                                    </td>
                                    <td data-label="Stok Ukuran">{{ $uk->stok_ukuran }}</td>
                                    <td data-label="Aksi" class="mobile-card-actions">
                                        <a href="{{ route('pegawai.eukuran', [$uk->id_barang, $uk->id_ukuran]) }}"
                                            class="btn btn-edit-outline btn-sm me-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal-{{ $uk->id_ukuran }}">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal-{{ $uk->id_ukuran }}" tabindex="-1"
                                            aria-labelledby="deleteModal-{{ $uk->id_ukuran }}Label" aria-hidden="true"
                                            data-bs-backdrop="static">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content bg-pink">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-4"
                                                            id="deleteModal-{{ $uk->id_ukuran }}Label">
                                                            Hapus Ukuran ?
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center my-4">
                                                        Hapus Ukuran {{ $uk->nama_ukuran }} ?
                                                    </div>
                                                    <div class="modal-footer mx-auto">
                                                        <form action="{{ route('pegawai.delukuran', $uk->id_ukuran) }}"
                                                            method="post">
                                                            @csrf
                                                            <button class="btn btn-delete btn-sm" type="submit">
                                                                <i class="fa-solid fa-trash"></i> HAPUS
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-green btn-sm" data-bs-dismiss="modal">
                                                            <i class="fa-solid fa-xmark"></i> BATAL
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
