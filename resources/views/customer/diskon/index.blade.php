@extends('customer.layouts.app')

@section('title', 'Diskon - Toko XYZ')
@php $activ = 'profil'; @endphp

@section('content')
    <div class="main-content content-narrow">
        <div class="mobile-page-head">
            <a href="{{ route('profil') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title mb-0">Diskon</h1>
            <span class="d-none d-md-block" aria-hidden="true"></span>
        </div>

        @if ($diskons->isEmpty())
            <div class="card-pink p-4 text-center">
                <i class="fa-solid fa-tag fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">Tidak ada diskon aktif saat ini.</p>
            </div>
        @else
            <div class="vstack gap-3">
                @foreach ($diskons as $ds)
                    <div class="card-pink p-4 position-relative">
                        @if (in_array($ds->id_diskon, $notifiedIds, true))
                            <span class="position-absolute rounded-circle" title="Baru untuk Anda"
                                style="top:0.6rem; left:0.6rem; width:0.7rem; height:0.7rem; background:#dc3545; box-shadow:0 0 0 2px #fff;"></span>
                        @endif
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-tag text-warning"></i>
                                {{ $ds->nama_diskon }}
                            </h6>
                            <span class="badge text-bg-success">Diskon {{ (int) $ds->jumlah_diskon }}%</span>
                        </div>
                        <p class="mb-2 small text-muted">
                            Pakai kode <code class="fw-bold text-danger">{{ $ds->kode_diskon }}</code> saat checkout.
                        </p>
                        <p class="mb-0 small">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($ds->mulai_diskon)->translatedFormat('d F Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($ds->akhir_diskon)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
