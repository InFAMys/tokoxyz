@extends('pegawai.layouts.app')

@section('title', 'Edit Akun Pegawai - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-title">Edit Profil</div>
            <form action="{{ route('pegawai.update.nama') }}" method="post">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="nama_pegawai" class="form-label-pink">Nama</label>
                    <input id='nama_pegawai' name="nama_pegawai" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_pegawai', $pegawai->nama_pegawai) }}" placeholder="Nama" required />
                    @error('nama_pegawai')
                        <label for="nama_pegawai" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-4">
                    <i class="fa-regular fa-id-badge"></i> Ubah Nama
                </button>
            </form>
            <hr>
            <form action="{{ route('pegawai.update.username') }}" method="post">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="username_pegawai" class="form-label-pink">Username</label>
                    <input id='username_pegawai' name="username_pegawai" type="text"
                        class="form-control form-control-pink"
                        value="{{ old('username_pegawai', $pegawai->username_pegawai) }}" placeholder="Username" required />
                    @error('username_pegawai')
                        <label for="username_pegawai" class="form-label-pink text-danger mt-2">
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
            <hr>
            <form action="{{ route('pegawai.update.password') }}" method="post">
                @csrf
                @method('PUT')
                
                <div class="mb-2">
                    <label for="current_password" class="form-label-pink">Password Sekarang</label>
                    <input id="current_password" name="current_password" type="password"
                        class="form-control form-control-pink" placeholder="Password Sekarang" required />
                    @error('current_password')
                        <label for="current_password" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label-pink">Password Baru</label>
                    <input id="password" name="password" type="password" class="form-control form-control-pink"
                        placeholder="Password Baru" required />
                    @error('password')
                        <label for="password" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-key"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
@endsection

