@extends('pegawai.layouts.app')

@section('title', 'Dashboard Pegawai - Toko XYZ')

@php
    $activ = 'dashboard';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Dashboard Pegawai</h1>
            <div class="card-pink p-3">
                <h5 class="fw-bold text-pink mb-3">Pesanan Baru</h5>
                <div class="table-responsive">
                    <table class="table table-pink table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode Pesanan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesananBaru as $po)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $po->order_id }}</td>
                                <td>
                                    <span class="badge rounded-pill text-bg-{{ $po->statusColor() }}">
                                        {{ $po->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pegawai.detailpesanan', $po->id_checkout) }}"
                                        class="btn btn-pink-outline btn-sm">
                                        <i class="fa-solid fa-circle-info"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada pesanan baru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
