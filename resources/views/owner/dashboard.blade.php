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
                    <div class="stat-value">Rp 12,4jt</div>
                    <div class="stat-label">Total Penjualan Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                    <div class="stat-value">84</div>
                    <div class="stat-label">Pesanan Bulan Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-value">{{ \App\Models\Pegawai::count() }}</div>
                    <div class="stat-label">Total Pegawai</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                    <div class="stat-value">3</div>
                    <div class="stat-label">Diskon Aktif</div>
                </div>
            </div>
        </div>

        <div class="card-pink p-3">
            <div class="filter-bar">
                <h5 class="mb-0 fw-bold text-pink me-auto">Laporan Penjualan</h5>
                <input type="date" class="form-control form-control-pink" style="width: 160px" />
                <span class="fw-semibold text-muted small">s/d</span>
                <input type="date" class="form-control form-control-pink" style="width: 160px" />
                <button class="btn btn-pink">Filter</button>
            </div>
            <div class="table-responsive">
                <table class="table table-pink table-borderless mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Laporan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Laporan Penjualan 01 Mei 2025</td>
                        <td>01 Mei 2025</td>
                        <td>Rp 2.350.000</td>
                        <td>
                            <button class="btn btn-pink-outline btn-sm">Buka</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Laporan Penjualan 02 Mei 2025</td>
                        <td>02 Mei 2025</td>
                        <td>Rp 1.780.000</td>
                        <td>
                            <button class="btn btn-pink-outline btn-sm">Buka</button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Laporan Penjualan 03 Mei 2025</td>
                        <td>03 Mei 2025</td>
                        <td>Rp 3.110.000</td>
                        <td>
                            <button class="btn btn-pink-outline btn-sm">Buka</button>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Laporan Penjualan 04 Mei 2025</td>
                        <td>04 Mei 2025</td>
                        <td>Rp 900.000</td>
                        <td>
                            <button class="btn btn-pink-outline btn-sm">Buka</button>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Laporan Penjualan 05 Mei 2025</td>
                        <td>05 Mei 2025</td>
                        <td>Rp 4.260.000</td>
                        <td>
                            <button class="btn btn-pink-outline btn-sm">Buka</button>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
