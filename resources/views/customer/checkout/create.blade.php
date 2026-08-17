@extends('customer.layouts.app')

@section('title', 'Checkout - Toko XYZ')
@php $activ = 'keranjang'; @endphp

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <h1 class="page-title mb-0">Checkout</h1>
            <a href="{{ route('keranjang.index') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-3 text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form"
            data-rate-url="{{ route('checkout.rate') }}" data-diskon-url="{{ route('checkout.diskon') }}"
            data-member-diskon="{{ $memberDiskon }}">
            @csrf

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-pink p-3 mb-4">
                        <div class="form-label-pink mb-2">Alamat Pengiriman</div>
                        @if ($alamats->isEmpty())
                            <p class="text-muted mb-2">Belum ada alamat. <a href="{{ route('alamat.create') }}">Tambah
                                    alamat</a></p>
                        @else
                            @php $defaultAlamat = optional($alamats->firstWhere('id_kecamatan', '!=', null))->id_alamat; @endphp
                            @foreach ($alamats as $alamat)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="id_alamat"
                                        value="{{ $alamat->id_alamat }}" id="alamat-{{ $alamat->id_alamat }}"
                                        @checked(old('id_alamat', $defaultAlamat) == $alamat->id_alamat) @if (!$alamat->id_kecamatan) disabled @endif>
                                    <label class="form-check-label" for="alamat-{{ $alamat->id_alamat }}">
                                        <strong>{{ $alamat->nama_alamat }}</strong> – {{ $alamat->nama_penerima }}
                                        ({{ $alamat->telp_penerima }})
                                        <br>
                                        <small class="text-muted">{{ $alamat->detail_alamat }}, {{ $alamat->kecamatan }},
                                            {{ $alamat->kota }}, {{ $alamat->provinsi }} {{ $alamat->kode_pos }}</small>
                                        @if (!$alamat->id_kecamatan)
                                            <span class="badge text-bg-warning ms-1">Lengkapi kecamatan (Klikresi)</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="card-pink p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="form-label-pink mb-0">Ongkir J&T</div>
                            <button type="button" id="cek-ongkir" class="btn btn-pink btn-sm">
                                <i class="fa-solid fa-truck-fast"></i> Cek Ongkir
                            </button>
                        </div>
                        <div id="shipping-error" class="text-danger small mb-1" style="display:none;"></div>
                        <div id="shipping-options" class="mb-2 text-muted">
                            Pilih alamat lalu klik "Cek Ongkir".
                        </div>
                    </div>

                    <div class="card-pink p-3">
                        <div class="form-label-pink mb-2">Kode Diskon</div>
                        <div class="input-group mb-2">
                            <input type="text" name="kode_diskon" id="kode-diskon" class="form-control form-control-pink"
                                placeholder="Masukkan kode diskon" value="{{ old('kode_diskon') }}" maxlength="10">
                            <button type="button" id="terapkan-diskon" class="btn btn-pink-outline">Terapkan</button>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="diskon-info" class="small"></div>
                            <button type="button" id="hapus-diskon" class="btn btn-sm btn-outline-danger"
                                style="display:none;">
                                <i class="fa-solid fa-xmark"></i> Hapus
                            </button>
                        </div>
                        @if (isset($customer) && $customer->member === 'true')
                            <div class="text-muted small mt-2">
                                <i class="fa-solid fa-circle-info"></i> Saat memakai kode diskon, diskon member 10% tidak
                                berlaku.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-box">
                        <div class="form-label-pink mb-2">Barang</div>
                        <div class="mb-3">
                            @foreach ($items as $item)
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>
                                        {{ $item['barang']->nama_barang }}
                                        @if ($item['ukuran_name'])
                                            <small class="text-muted">({{ $item['ukuran_name'] }})</small>
                                        @endif
                                        × {{ $item['jumlah_barang'] }}
                                    </span>
                                    <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="summary-row">
                            <span>Berat Total</span>
                            <span>{{ rtrim(rtrim(number_format($beratTotal, 1, ',', '.'), '0'), ',') }} kg</span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="sum-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="summary-row" id="row-diskon">
                            <span><span id="diskon-label">{{ $memberDiskon > 0 ? 'Diskon Member' : 'Diskon' }}</span> <span id="diskon-persen-label">{{ $memberDiskon > 0 ? '(10%)' : '' }}</span></span>
                            <span id="sum-diskon" class="text-success">-</span>
                        </div>
                        <div class="summary-row" id="row-ongkir">
                            <span>Ongkir</span>
                            <span id="sum-ongkir">-</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="sum-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <input type="hidden" name="shipping_service" id="shipping-service">
                        <input type="hidden" name="shipping_cost" id="shipping-cost">
                        <input type="hidden" id="subtotal-raw" value="{{ $subtotal }}">
                        <input type="hidden" id="ongkir-raw" value="0">
                        <input type="hidden" id="diskon-raw" value="{{ $memberDiskon }}">
                        <button type="submit" id="bayar-submit" class="btn btn-pink w-100 mt-3">
                            <i class="fa-solid fa-receipt"></i> Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
