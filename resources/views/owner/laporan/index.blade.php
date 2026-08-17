@extends('owner.layouts.app')

@section('title', 'Laporan Penjualan - Toko XYZ')
@php
    $activ = 'laporan';
@endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="page-title mb-0">Laporan Penjualan</h1>
                <small class="text-muted">{{ $from->format('d M Y') }} s/d {{ $to->format('d M Y') }}</small>
            </div>
            <button type="button" class="btn btn-pink-outline d-print-none" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
        </div>

        <div class="card-pink p-3 mb-4 d-print-none">
            <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <input type="date" name="from" class="form-control form-control-pink" style="width: 160px"
                    value="{{ $from->format('Y-m-d') }}">
                <span class="fw-semibold text-muted small">s/d</span>
                <input type="date" name="to" class="form-control form-control-pink" style="width: 160px"
                    value="{{ $to->format('Y-m-d') }}">
                <button class="btn btn-pink">Filter</button>
            </form>
        </div>

        <div class="laporan-grid mb-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-money-bills"></i></div>
                <div class="stat-value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
                <div class="stat-value">{{ $summary['orders'] }}</div>
                <div class="stat-label">Jumlah Pesanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-value">Rp {{ number_format($summary['avgOrder'], 0, ',', '.') }}</div>
                <div class="stat-label">Rata-rata Pesanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <div class="stat-value">{{ $summary['itemsSold'] }}</div>
                <div class="stat-label">Barang Terjual</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value">{{ $summary['preorderOrders'] }}</div>
                <div class="stat-label">Pesanan Preorder</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
                <div class="stat-value">Rp {{ number_format($summary['discountsGiven'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Diskon</div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-pink p-3 h-100">
                    <h5 class="fw-bold text-pink mb-3">Top Produk</h5>
                    <div class="table-responsive">
                        <table class="table table-pink table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topProducts as $p)
                                    <tr>
                                        <td>{{ $p->nama_barang }}</td>
                                        <td>{{ $p->qty }}</td>
                                        <td>Rp {{ number_format($p->revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-pink p-3 h-100">
                    <h5 class="fw-bold text-pink mb-3">Per Kategori</h5>
                    <div class="table-responsive">
                        <table class="table table-pink table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Qty</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $c)
                                    <tr>
                                        <td>{{ $c->nama_kategori }}</td>
                                        <td>{{ $c->qty }}</td>
                                        <td>Rp {{ number_format($c->revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-pink p-3">
            <h5 class="fw-bold text-pink mb-3">Detail Pesanan</h5>
            <div class="table-responsive">
                <table class="table table-pink table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Ongkir</th>
                            <th>Total</th>
                            <th class="print-hide">Tipe</th>
                            <th class="print-hide">Status</th>
                            <th class="print-hide">Kritik & Saran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $s->order_id }}</td>
                                <td>{{ $s->paid_at?->format('d M Y H:i') }}</td>
                                <td>{{ $s->customer_name }}</td>
                                <td>Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                                <td>- Rp {{ number_format($s->diskon_nominal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($s->shipping_cost, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td class="print-hide">
                                    @if ($s->items->contains('is_preorder', true))
                                        <span class="badge rounded-pill text-bg-warning">Preorder</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="print-hide">
                                    <span class="badge rounded-pill text-bg-{{ $s->statusColor() }}">
                                        {{ $s->statusLabel() }}
                                    </span>
                                </td>
                                <td class="print-hide">{{ $s->kritik_saran ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Tidak ada pesanan pada rentang
                                    tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .laporan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            grid-auto-rows: 1fr;
            gap: 1rem;
        }
        @media print {
            body {
                background: #fff !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
                color: #000 !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .d-print-none {
                display: none !important;
            }
            .print-hide {
                display: none !important;
            }
            .card-pink {
                box-shadow: none !important;
                border: none !important;
                background: #fff !important;
            }
            .stat-card {
                box-shadow: none !important;
                border: none !important;
                background: #fff !important;
            }
            .stat-card .stat-icon {
                display: none !important;
            }
            .stat-card .stat-icon,
            .stat-card .stat-value,
            .stat-card .stat-label {
                color: #000 !important;
                font-family: inherit !important;
            }
            .page-title {
                color: #000 !important;
                font-family: inherit !important;
            }
            .card-pink,
            .card-pink * {
                color: #000 !important;
                font-family: inherit !important;
            }
            .table-pink thead {
                background: #fff !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .table-responsive > .table {
                min-width: 0 !important;
            }
            .table-pink tbody tr {
                border-bottom: 1px solid #ccc !important;
            }
            .table-pink thead th,
            .table-pink tbody td,
            .table-pink th,
            .table-pink td {
                border: none !important;
                background: #fff !important;
                color: #000 !important;
            }
        }
    </style>
@endsection
