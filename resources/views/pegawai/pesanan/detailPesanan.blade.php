@extends('pegawai.layouts.app')

@section('title', 'Detail Pesanan ' . $checkout->order_id . ' - Toko XYZ')
@php $activ = 'pesanan'; @endphp

@section('content')
    <div class="d-flex" style="flex: 1">
        <div class="main-content">
            <div class="mb-3">
                <a href="{{ route('pegawai.pesanan') }}" class="btn btn-pink-outline btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card-pink p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                    <h1 class="h4 mb-0">Pesanan {{ $checkout->order_id }}</h1>
                    <span class="badge rounded-pill text-bg-{{ $checkout->statusColor() }}">
                        {{ $checkout->statusLabel() }}
                    </span>
                </div>

                @if ($checkout->status === 'pending')
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-clock"></i> Pesanan otomatis dibatalkan pada
                        <strong>{{ $checkout->created_at->addHours(24)->format('d M Y H:i') }}</strong>
                        jika pembayaran belum diterima.
                    </div>
                @elseif ($checkout->status === 'paid')
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-clock"></i> Pesanan otomatis dibatalkan (dana direfund) pada
                        <strong>{{ optional($checkout->paid_at)->addDays(3)->format('d M Y H:i') }}</strong>
                        jika belum diproses.
                    </div>
                @endif

                <div class="summary-box mb-3">
                    <div class="form-label-pink">Customer</div>
                    <div>{{ $checkout->customer_name }} · {{ $checkout->customer_telp }}</div>
                    <div class="small text-muted">{{ $checkout->customer_email }}</div>
                    @if ($checkout->pegawai)
                        <div class="small text-muted mt-1">Diproses oleh: {{ $checkout->pegawai->nama_pegawai }}</div>
                    @endif
                </div>

                <div class="summary-box mb-3">
                    <div class="form-label-pink">Alamat Pengiriman</div>
                    <div>{{ $checkout->shipping_address }}</div>
                    <div class="small text-muted">
                        {{ $checkout->shipping_courier }} {{ $checkout->shipping_service }}
                        @if ($checkout->berat_total > 0)
                            · {{ rtrim(rtrim(number_format($checkout->berat_total, 3, ',', '.'), '0'), ',') }} kg
                        @endif
                    </div>
                </div>

                <div class="summary-box mb-3">
                    <div class="form-label-pink">Barang</div>
                    @foreach ($checkout->items as $item)
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $item->nama_barang }} @if ($item->ukuran_name)
                                    ({{ $item->ukuran_name }})
                                @endif × {{ $item->jumlah_barang }}
                                @if ($item->is_preorder)
                                    <span class="badge text-bg-warning ms-1">Preorder
                                        @if ($item->estimasi_preorder)
                                            ~{{ $item->estimasi_preorder }} hari
                                        @endif
                                    </span>
                                @endif
                            </span>
                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="summary-box mb-3">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($checkout->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($checkout->diskon_nominal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkir</span>
                        <span>Rp {{ number_format($checkout->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>Rp {{ number_format($checkout->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($checkout->status === 'cancel_pending')
                    <div class="summary-box mb-3">
                        <div class="form-label-pink">Permintaan Pembatalan</div>
                        <div>Alasan customer: {{ $checkout->cancel_reason ?? '-' }}</div>
                        <div class="small text-muted">
                            Diajukan {{ optional($checkout->cancel_requested_at)->format('d M Y H:i') ?? '-' }} ·
                            Status sebelumnya: {{ $checkout->cancel_from ?? '-' }}
                            @if ($checkout->cancel_from === 'paid' || $checkout->cancel_from === 'processed')
                                (akan refund)
                            @endif
                        </div>
                    </div>

                    <button type="button" class="btn btn-delete w-100 mb-2" data-bs-toggle="modal"
                        data-bs-target="#cancelModal">
                        <i class="fa-solid fa-xmark"></i> Proses Pembatalan
                    </button>

                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Proses Pembatalan {{ $checkout->order_id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <form method="POST"
                                        action="{{ route('pegawai.cancelapprovepesanan', $checkout->id_checkout) }}"
                                        id="approve-form">
                                        @csrf

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-bold">
                                                    @if ($checkout->cancel_from === 'paid' || $checkout->cancel_from === 'processed')
                                                        Setujui Pembatalan (Refund Dana)
                                                    @else
                                                        Setujui Pembatalan
                                                    @endif
                                                </div>
                                                @if ($checkout->cancel_from === 'paid' || $checkout->cancel_from === 'processed')
                                                    <div class="small text-muted">
                                                        Dana dikembalikan ke customer ({{ $checkout->cancel_from }} → refund).
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-delete">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </div>
                                    </form>

                                    <hr>

                                    <form method="POST"
                                        action="{{ route('pegawai.cancelrejectpesanan', $checkout->id_checkout) }}"
                                        id="reject-form">
                                        @csrf
                                        <label class="form-label-pink">Tolak Pembatalan</label>
                                        <textarea name="cancel_response" rows="2" maxlength="255"
                                            class="form-control form-control-pink mb-2 @error('cancel_response') is-invalid @enderror"
                                            placeholder="Alasan menolak pembatalan (wajib)">{{ old('cancel_response') }}</textarea>
                                        @error('cancel_response')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <button type="submit" class="btn btn-delete-outline w-100">
                                            <i class="fa-solid fa-ban"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($checkout->status === 'paid')
                    <form method="POST" action="{{ route('pegawai.prosespesanan', $checkout->id_checkout) }}">
                        @csrf
                        <button type="submit" class="btn btn-pink w-100">
                            <i class="fa-solid fa-check"></i> Konfirmasi & Proses Pesanan
                        </button>
                    </form>
                @endif

                @if ($checkout->status === 'processed' || $checkout->status === 'shipping')
                    <form method="POST" action="{{ route('pegawai.kirimpesanan', $checkout->id_checkout) }}">
                        @csrf
                        <label class="form-label-pink mt-2">No Resi</label>
                        <input type="text" name="no_resi" value="{{ old('no_resi', $checkout->no_resi) }}"
                            class="form-control form-control-pink @error('no_resi') is-invalid @enderror"
                            placeholder="Masukkan no resi pengiriman" maxlength="50" @disabled($checkout->status === 'shipping') />
                        @error('no_resi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($checkout->status === 'processed')
                            <button type="submit" class="btn btn-pink w-100 mt-2">
                                <i class="fa-solid fa-truck"></i> Kirim Pesanan
                            </button>
                        @endif
                    </form>
                @endif

                @if ($checkout->status !== 'paid' && $checkout->status !== 'processed' && $checkout->status !== 'shipping')
                    <div class="alert alert-success mb-0 mt-3">
                        <i class="fa-solid fa-circle-check"></i> Status pesanan: {{ $checkout->statusLabel() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
