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
                        <tr>
                            <td>1</td>
                            <td>#PSN-20250527-001</td>
                            <td>
                                <span class="badge-status badge-pending">Menunggu Konfirmasi</span>
                            </td>
                            <td>
                                <button class="btn btn-pink-outline btn-sm">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>#PSN-20250527-002</td>
                            <td>
                                <span class="badge-status badge-pending">Menunggu Konfirmasi</span>
                            </td>
                            <td>
                                <button class="btn btn-pink-outline btn-sm">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>#PSN-20250527-003</td>
                            <td>
                                <span class="badge-status badge-pending">Menunggu Konfirmasi</span>
                            </td>
                            <td>
                                <button class="btn btn-pink-outline btn-sm">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
