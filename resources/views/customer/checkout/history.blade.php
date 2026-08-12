@extends('customer.layouts.app')

@section('title', 'Riwayat Pesanan - Toko XYZ')
@php $activ = 'pesanan'; @endphp

@section('content')
    <div class="main-content">
        <h1 class="page-title mb-4">Riwayat Pesanan</h1>

        <div class="card-pink p-3">
            @if ($checkouts->isEmpty())
                <div class="text-center py-5">
                    <h2 class="h4 mb-2">Belum ada pesanan</h2>
                    <p class="text-muted">Mulai belanja dan buat pesanan pertamamu.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-pink table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checkouts as $i => $checkout)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $checkout->order_id }}</td>
                                    <td>{{ $checkout->created_at->format('d M Y H:i') }}</td>
                                    <td>Rp {{ number_format($checkout->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-{{ $checkout->status === 'paid' ? 'success' : ($checkout->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($checkout->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('checkout.show', $checkout->id_checkout) }}" class="btn btn-edit-outline btn-sm">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
