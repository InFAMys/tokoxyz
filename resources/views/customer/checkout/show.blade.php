@extends('customer.layouts.app')

@section('title', 'Checkout #' . $checkout->order_id . ' - Toko XYZ')
@php $activ = 'pesanan'; @endphp

@section('content')
    <div class="main-content">
        <div class="mb-4">
            <a href="{{ route('checkout.history') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>

        <div class="card-pink p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <h1 class="h4 mb-0">Pesanan {{ $checkout->order_id }}</h1>
                <span class="badge rounded-pill text-bg-{{ $checkout->statusColor() }}">
                    {{ $checkout->statusLabel() }}
                </span>
            </div>

            @if (in_array($checkout->status, ['paid', 'processed', 'shipping', 'delivered', 'completed'], true))
                @php
                    $steps = [
                        'paid' => 'Pembayaran',
                        'processed' => 'Diproses',
                        'shipping' => 'Pengiriman',
                        'delivered' => 'Sampai Tujuan',
                        'completed' => 'Selesai',
                    ];
                    $order = ['paid', 'processed', 'shipping', 'delivered', 'completed'];
                    $current = $checkout->status === 'completed' ? 5 : array_search($checkout->status, $order);
                @endphp
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    @foreach ($order as $i => $state)
                        <div class="text-center {{ $i + 1 <= $current ? 'text-pink fw-bold' : 'text-muted' }}"
                            style="flex:1 1 8rem">
                            <div class="mb-1">{{ $steps[$state] }}</div>
                            <i class="fa-solid {{ $i + 1 <= $current ? 'fa-circle-check' : 'fa-regular fa-circle' }}"></i>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (in_array($checkout->status, ['shipping', 'delivered', 'completed'], true) && $checkout->no_resi)
                <div class="summary-box mb-3">
                    <div class="form-label-pink">No Resi Pengiriman</div>
                    <div class="fw-bold">{{ $checkout->no_resi }}
                        <span class="small text-muted">({{ $checkout->shipping_courier }}
                            {{ $checkout->shipping_service }})</span>
                    </div>
                    <div class="small text-muted">Status pengiriman diperbarui otomatis.</div>
                </div>
            @endif

            <div class="summary-box mb-3">
                <div class="form-label-pink">Alamat Pengiriman</div>
                <div>{{ $checkout->shipping_address }}</div>
                <div class="small text-muted">
                    {{ $checkout->shipping_courier }} {{ $checkout->shipping_service }}
                    @if ($checkout->berat_total > 0)
                        · {{ rtrim(rtrim(number_format($checkout->berat_total, 1, ',', '.'), '0'), ',') }} kg
                    @endif
                </div>
            </div>

            <div class="summary-box mb-3">
                <div class="form-label-pink">Barang</div>
                @foreach ($checkout->items as $item)
                    <div class="d-flex justify-content-between gap-3 mb-1">
                        <span>{{ $item->nama_barang }} × {{ $item->jumlah_barang }}
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
                    <span>{{ $checkout->member_diskon_nominal ? 'Diskon Member 10%' : 'Diskon' }}</span>
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

            @if ($checkout->kritik_saran)
                <div class="summary-box mb-3">
                    <div class="form-label-pink">Kritik & Saran Anda</div>
                    <div>{{ $checkout->kritik_saran }}</div>
                </div>
            @endif

            @if ($checkout->status === 'cancel_pending')
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-clock"></i> Permintaan pembatalan sedang menunggu konfirmasi admin.
                    <div class="small mt-1">Alasan: {{ $checkout->cancel_reason ?? '-' }}</div>
                </div>
            @endif

            @if ($checkout->status === 'pending')
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-clock"></i> Pesanan otomatis dibatalkan pada
                    <strong>{{ $checkout->created_at->addHours(24)->format('d M Y H:i') }}</strong>
                    jika pembayaran belum diterima.
                </div>
            @elseif ($checkout->status === 'paid')
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-clock"></i> Pesanan otomatis dibatalkan (dana direfund) pada
                    <strong>{{ optional($checkout->paid_at)->addDays(3)->format('d M Y H:i') }}</strong>
                    jika belum diproses.
                </div>
            @endif

            @if ($checkout->status === 'delivered')
                <form method="POST" action="{{ route('checkout.confirm', $checkout->id_checkout) }}" id="confirm-form">
                    @csrf
                    <button type="button" class="btn btn-pink w-100" data-bs-toggle="modal"
                        data-bs-target="#confirmModal">
                        <i class="fa-solid fa-check"></i> Konfirmasi Pesanan Diterima
                    </button>
                </form>
                <p class="text-muted small mt-2 mb-0">
                    Jika ada masalah dengan pesanan, hubungi kami via
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo, saya ingin komplain pesanan {{ $checkout->order_id }}"
                        target="_blank" rel="noopener">
                        WhatsApp
                    </a>. Pesanan otomatis selesai dalam 7 hari.
                </p>

                <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pesanan Diterima</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Yakin pesanan
                                    <strong>{{ $checkout->order_id }}</strong> sudah diterima dengan baik?
                                </p>
                                <label class="form-label-pink">Kritik & Saran <span class="text-muted small">(opsional)</span></label>
                                <textarea name="kritik_saran" form="confirm-form" rows="3" maxlength="2000"
                                    class="form-control form-control-pink" placeholder="Tulis masukan Anda untuk kami..."></textarea>
                                @error('kritik_saran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-pink-outline" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" form="confirm-form" class="btn btn-pink">
                                    <i class="fa-solid fa-check"></i> Ya, Selesaikan Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($checkout->status === 'pending' && $checkout->snap_token)
                <button type="button" id="bayar-button" class="btn btn-pink w-100"
                    data-checkout-token="{{ $checkout->snap_token }}"
                    data-client-key="{{ config('services.midtrans.client_key') }}"
                    data-prod="{{ config('services.midtrans.is_production') ? 1 : 0 }}">
                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                </button>
                <p class="text-muted small mt-2 mb-0">Setelah pembayaran, status akan diperbarui otomatis.</p>
            @elseif (in_array($checkout->status, ['refunded', 'partially_refunded'], true))
                <p class="text-muted small mt-2 mb-0">
                    Dana mungkin butuh beberapa hari untuk kembali. Jika ada kendala, hubungi kami via
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Halo, saya ingin tanya refund pesanan {{ $checkout->order_id }}"
                        target="_blank" rel="noopener">
                        WhatsApp
                    </a>.
                </p>
            @elseif ($checkout->status === 'pending')
                <div class="alert alert-info mb-0">Pembayaran belum bisa dimulai kembali. Hubungi admin.</div>
            @else
                <div class="alert alert-success mb-0">
                    <i class="fa-solid fa-circle-check"></i> Status pesanan: {{ $checkout->statusLabel() }}
                </div>
            @endif

            @if (in_array($checkout->status, \App\Models\Checkout::cancellableStatuses(), true))
                @if ($checkout->cancel_response)
                    <div class="alert alert-danger mt-3 mb-2">
                        <i class="fa-solid fa-circle-xmark"></i> Permintaan pembatalan ditolak.
                        <div class="small mt-1">Alasan: {{ $checkout->cancel_response }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.cancel', $checkout->id_checkout) }}" id="cancel-form"
                    class="mt-3">
                    @csrf
                    @error('cancel')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <button type="button" class="btn btn-delete w-100" data-bs-toggle="modal"
                        data-bs-target="#cancelModal">
                        <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                    </button>
                </form>

                <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pembatalan Pesanan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Yakin ingin membatalkan pesanan
                                    <strong>{{ $checkout->order_id }}</strong>?
                                    <span class="d-block small text-muted">
                                        @if ($checkout->status === 'pending')
                                            Pesanan akan langsung dibatalkan.
                                        @else
                                            Admin akan memverifikasi permintaan Anda.
                                        @endif
                                    </span>
                                </p>
                                @if ($checkout->status !== 'pending')
                                    <textarea name="cancel_reason" form="cancel-form" rows="2" maxlength="255"
                                        class="form-control form-control-pink @error('cancel_reason') is-invalid @enderror" placeholder="Alasan pembatalan"
                                        required>{{ old('cancel_reason') }}</textarea>
                                    @error('cancel_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-pink-outline" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" form="cancel-form" class="btn btn-delete">
                                    <i class="fa-solid fa-check"></i> Ya, Batalkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
