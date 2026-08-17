@extends('owner.layouts.app')

@section('title', 'Dashboard Owner - Toko XYZ')
@php
    $activ = 'home';
@endphp

@section('content')

    <div class="main-content">
        <h1 class="page-title">Dashboard</h1>
        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-money-bills"></i></div>
                    <div class="stat-value">Rp {{ number_format($stats['revenueThisMonth'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Penjualan Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-value">{{ $stats['ordersThisMonth'] }}</div>
                    <div class="stat-label">Pesanan Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-value">{{ $totalPegawai }}</div>
                    <div class="stat-label">Total Pegawai</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                    <div class="stat-value">{{ $activeDiskons }}</div>
                    <div class="stat-label">Diskon Aktif</div>
                </div>
            </div>
        </div>

        <div class="card-pink p-3">
            <div class="filter-bar">
                <h5 class="mb-0 fw-bold text-pink me-auto">Laporan Penjualan</h5>
                <a href="{{ route('owner.laporan') }}" class="btn btn-pink">Buka Laporan Lengkap</a>
            </div>
            <div class="table-responsive mobile-card-responsive">
                <table class="table table-pink table-borderless mobile-card-table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Order ID</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stats['recentSales'] as $s)
                        <tr>
                            <td data-label="No">{{ $loop->iteration }}</td>
                            <td data-label="Order ID">{{ $s->order_id }}</td>
                            <td data-label="Tanggal">{{ $s->paid_at?->format('d M Y H:i') }}</td>
                            <td data-label="Customer">{{ $s->customer_name }}</td>
                            <td data-label="Total">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                            <td data-label="Tipe">
                                @if ($s->items->contains('is_preorder', true))
                                    <span class="badge rounded-pill text-bg-warning">Preorder</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada pesanan bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
