@extends('customer.layouts.app')

@section('title', 'Edit Profil - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <a class="btn btn-pink-outline mb-4" href="{{ route('profil') }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="auth-title">Edit Profil</div>
            <form action="{{ route('profil.update.nama') }}" method="post">
                @csrf
                @method('PUT')
                {{-- @if (session('nstatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('nstatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-4">
                    <label for="nama" class="form-label-pink">Nama Lengkap</label>
                    <input id='nama' name="nama" type="text" class="form-control form-control-pink"
                        value="{{ old('nama', $customer->nama) }}" placeholder="Nama Lengkap" maxlength="64" required />
                    @error('nama')
                        <label for="nama" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-4">
                    <i class="fa-regular fa-id-badge"></i> Ubah Nama
                </button>
            </form>
            <hr>
            <form action="{{ route('profil.update.email') }}" method="post">
                @csrf
                @method('PUT')
                {{-- @if (session('estatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('estatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-4">
                    <label for="email" class="form-label-pink">E-Mail</label>
                    <input id='email' name="email" type="email" class="form-control form-control-pink"
                        value="{{ old('email', $customer->email) }}" placeholder="Email" required />
                    @error('email')
                        <label for="email" class="form-label-pink text-danger mt-2">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-4">
                    <i class="fa-solid fa-envelope"></i> Ubah E-Mail
                </button>
            </form>
            <hr>
            <form action="{{ route('profil.update.telp') }}" method="post">
                @csrf
                @method('PUT')
                {{-- @if (session('ntstatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('ntstatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-4">
                    <label for="no_telp" class="form-label-pink">No. Telepon</label>
                    <input id="no_telp" name="no_telp" type="tel" class="form-control form-control-pink"
                        value="{{ old('no_telp', $customer->no_telp) }}" placeholder="08xx-xxxx-xxxx" inputmode="numeric"
                        pattern="[0-9\-]{9,15}" maxlength="12" oninput="this.value = this.value.replace(/[^0-9\-]/g, '')"
                        required />
                    @error('no_telp')
                        <label for="no_telp" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-4">
                    <i class="fa-solid fa-phone"></i> Ubah No. Telepon
                </button>
            </form>
            <hr>
            <form action="{{ route('profil.update.username') }}" method="post">
                @csrf
                @method('PUT')
                {{-- @if (session('ustatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('ustatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-4">
                    <label for="username" class="form-label-pink">Username</label>
                    <input id='username' name="username" type="text" class="form-control form-control-pink"
                        value="{{ old('username', $customer->username) }}" placeholder="Username" maxlength="15"
                        required />
                    @error('username')
                        <label for="username" class="form-label-pink text-danger mt-2">
                            {{ $message }}
                        </label>
                    @else
                        <p class="form-label-rememberme mt-2 formhint">Hanya huruf, angka, garis bawah (_), dan tanda hubung (-)
                            yang diperbolehkan untuk Username.
                        </p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-4">
                    <i class="fa-solid fa-at"></i> Ubah Username
                </button>
            </form>
        </div>
    </div>
@endsection

