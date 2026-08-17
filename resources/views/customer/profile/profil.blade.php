@extends('customer.layouts.app')

@section('title', 'Profil Saya - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="main-content content-narrow-sm">
        <h1 class="page-title text-center">Profil Saya</h1>
        <div class="card p-4 text-center mb-3">
            <div class="avatar-circle"><i class="fa-solid fa-user"></i></div>
            <h5 class="fw-bold mb-0">{{ $customer->nama }}</h5>
            <hr>
            <p class="text-muted small">{{ $customer->username }} <br> {{ $customer->email }}</p>
        </div>
        <div class="card p-0 overflow-hidden">
            <a href="{{ route('membership.index') }}"
                class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom"
                style="border-bottom: 1px solid var(--pink-100) !important">
                <span class="me-3"><i
                        class="fa-solid fa-id-card {{ $customer->member === 'true' ? 'text-warning' : '' }}"></i></span>
                Member
                <span class="ms-auto text-muted">{{ $customer->member === 'true' ? 'Aktif' : 'Tidak Aktif' }}</span>
                <span class="ms-3 text-muted">›</span>
            </a>
            <a href="{{ route('alamat.index') }}"
                class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom"
                style="border-bottom: 1px solid var(--pink-100) !important">
                <span class="me-3"><i class="fa-solid fa-location-dot"></i></span>
                Alamat
                <span class="ms-auto text-muted">›</span>

            </a>
            <a href="{{ route('profil.edit') }}"
                class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom"
                style="border-bottom: 1px solid var(--pink-100) !important">
                <span class="me-3"><i class="fa-solid fa-user-edit"></i></span> Ubah
                Profil
                <span class="ms-auto text-muted">›</span>
            </a>
            <a href="{{ route('password.edit') }}" class="d-flex align-items-center p-3 text-decoration-none text-dark">
                <span class="me-3"><i class="fa-solid fa-key"></i></span> Ubah
                Password
                <span class="ms-auto text-muted">›</span>
            </a>
        </div>
    </div>
@endsection
