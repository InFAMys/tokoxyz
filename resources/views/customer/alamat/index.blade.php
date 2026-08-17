@extends('customer.layouts.app')

@section('title', 'Daftar Alamat - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="main-content content-narrow">
        <div class="d-grid d-md-flex flex-wrap justify-content-md-between align-items-md-center mb-5 gap-2">
            <a class="btn btn-pink-outline flex-shrink-0" href="{{ route('profil') }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title text-center mb-0">Alamat Saya</h1>
            <a class="btn btn-pink flex-shrink-0" href="{{ route('alamat.create') }}">
                <i class="fa-solid fa-plus"></i> Tambah
            </a>
        </div>

        

        @forelse ($alamat as $item)
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="flex-grow-1" style="min-width: 0">
                        <h6 class="fw-bold mb-1">
                            <i class="fa-solid fa-location-dot me-2 text-pink"></i>{{ $item->nama_alamat }}
                        </h6>
                        <p class="mb-1 small" style="word-break: break-word">{{ $item->nama_penerima }} ·
                            {{ $item->telp_penerima }}</p>
                        <p class="mb-1 small text-muted" style="word-break: break-word">{{ $item->detail_alamat }}</p>
                        <p class="mb-0 small text-muted" style="word-break: break-word">
                            {{ $item->kelurahan }}, {{ $item->kecamatan }}<br>
                            {{ $item->kota }}, {{ $item->provinsi }} {{ $item->kode_pos }}
                        </p>
                    </div>
                    <div class="d-flex flex-shrink-0 gap-2">
                        <a href="{{ route('alamat.edit', $item->id_alamat) }}" class="btn btn-sm btn-pink-outline">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button type="button" class="btn btn-delete btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteAlamatModal-{{ $item->id_alamat }}" aria-label="Hapus alamat">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteAlamatModal-{{ $item->id_alamat }}" tabindex="-1"
                aria-labelledby="deleteAlamatModal-{{ $item->id_alamat }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-pink">
                        <div class="modal-header">
                            <h1 class="modal-title fs-4" id="deleteAlamatModal-{{ $item->id_alamat }}Label">
                                Hapus Alamat?
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center my-4">
                            Alamat {{ $item->nama_alamat }} akan dihapus dari daftar alamat Anda.
                        </div>
                        <div class="modal-footer mx-auto">
                            <form method="POST" action="{{ route('alamat.destroy', $item->id_alamat) }}">
                                @csrf
                                <button type="submit" class="btn btn-delete btn-sm">
                                    <i class="fa-solid fa-trash"></i> HAPUS
                                </button>
                            </form>
                            <button type="button" class="btn btn-green btn-sm" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark"></i> BATAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-4 text-center text-muted">
                Belum ada alamat. Tambahkan alamat pengiriman terlebih dahulu.
            </div>
        @endforelse
    </div>
@endsection
