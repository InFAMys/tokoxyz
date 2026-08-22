@extends('pegawai.layouts.app')

@section('title', 'Dashboard Pegawai - Toko XYZ')

@php
    $activ = 'dashboard';
@endphp

@section('content')
    <div class="main-content">
        <h1 class="page-title">Dashboard Pegawai</h1>
        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-value">{{ $newCount }}</div>
                    <div class="stat-label">Pesanan Baru</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
                    <div class="stat-value">{{ $processedCount }}</div>
                    <div class="stat-label">Pesanan Diproses</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-ban"></i></div>
                    <div class="stat-value">{{ $cancelCount }}</div>
                    <div class="stat-label">Menunggu Pembatalan</div>
                </div>
            </div>
        </div>

        <div class="card-pink p-3">
            <div class="filter-bar">
                <h5 class="mb-0 fw-bold text-pink me-auto">Pesanan</h5>
                <a href="{{ route('pegawai.pesanan') }}" class="btn btn-pink">Buka Semua Pesanan</a>
            </div>
            <div class="table-responsive mobile-card-responsive">
                <table class="table table-pink table-borderless mobile-card-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode Pesanan</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesananBaru as $po)
                            <tr>
                                <td data-label="ID">{{ $loop->iteration }}</td>
                                <td data-label="Kode Pesanan">{{ $po->order_id }}</td>
                                <td data-label="Tipe">
                                    @if ($po->items->contains('is_preorder', true))
                                        <span class="badge rounded-pill text-bg-warning">Preorder</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    <span class="badge rounded-pill text-bg-{{ $po->statusColor() }}">
                                        {{ $po->statusLabel() }}
                                    </span>
                                </td>
                                <td data-label="Aksi" class="mobile-card-actions">
                                    <a href="{{ route('pegawai.detailpesanan', $po->id_checkout) }}"
                                        class="btn btn-pink-outline btn-sm">
                                        <i class="fa-solid fa-circle-info"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada pesanan baru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
