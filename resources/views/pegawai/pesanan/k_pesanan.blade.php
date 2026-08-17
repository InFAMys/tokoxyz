@extends('pegawai.layouts.app')

@section('title', 'Kelola Pesanan - Toko XYZ')

@php
    $activ = 'pesanan';
@endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <h1 class="page-title">Kelola Pesanan</h1>
            <div class="card-pink p-3">
                <div class="filter-bar">
                    <form method="GET" class="search-wrapper" style="width: 300px">
                        <span class="search-icon"><i class="fa-solid fa-filter"></i></span>
                        <select name="status" class="form-control form-control-pink"
                            onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach (\App\Models\Checkout::STATUSES as $code => $label)
                                <option value="{{ $code }}" @selected($filter === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-pink table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Tipe</th>
                                <th>No Resi</th>
                                <th>Status</th>
                                <th>Pemroses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pesanan as $po)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $po->order_id }}</td>
                                    <td>{{ $po->customer_name }}</td>
                                    <td>{{ $po->created_at->format('d M Y H:i') }}</td>
                                    <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($po->items->contains('is_preorder', true))
                                            <span class="badge rounded-pill text-bg-warning">Preorder</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $po->no_resi ?? '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-{{ $po->statusColor() }}">
                                            {{ $po->statusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $po->pegawai?->nama_pegawai ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('pegawai.detailpesanan', $po->id_checkout) }}"
                                            class="btn btn-edit-outline btn-sm">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">Tidak ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
