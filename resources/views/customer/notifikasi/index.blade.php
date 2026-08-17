@extends('customer.layouts.app')

@section('title', 'Notifikasi Diskon - Toko XYZ')
@php $activ = 'profil'; @endphp

@section('content')
    <div class="main-content content-narrow">
        <div class="mobile-page-head">
            <a href="{{ route('profil') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title mb-0">Notifikasi Diskon</h1>
            <span class="d-none d-md-block" aria-hidden="true"></span>
        </div>

        @if ($notifications->isEmpty())
            <div class="card-pink p-4 text-center">
                <i class="fa-solid fa-bell-slash fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">Tidak ada diskon aktif saat ini.</p>
            </div>
        @else
            <div class="vstack gap-3">
                @foreach ($notifications as $n)
                    <div class="card-pink p-4">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-tag text-warning"></i>
                                {{ $n->data['nama_diskon'] }}
                            </h6>
                            <span class="badge text-bg-success">Diskon {{ (int) $n->data['jumlah_diskon'] }}%</span>
                        </div>
                        <p class="mb-2 small text-muted">
                            Pakai kode <code class="fw-bold text-danger">{{ $n->data['kode_diskon'] }}</code> saat checkout.
                        </p>
                        <p class="mb-0 small">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($n->data['mulai_diskon'])->translatedFormat('d F Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($n->data['akhir_diskon'])->translatedFormat('d F Y') }}
                        </p>
                    </div>
                @endforeach
            </div>
            <nav class="mt-4">{{ $notifications->links() }}</nav>
        @endif
    </div>
@endsection
