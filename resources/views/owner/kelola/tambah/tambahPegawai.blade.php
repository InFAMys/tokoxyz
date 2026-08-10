@extends('owner.layouts.app')

@section('title', 'Tambah Akun Pegawai - Toko XYZ')
@php
    $activ = 'pegawai';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('owner.addpegawai') }}" method="post">
                @csrf
                <div class="auth-title">Tambah Akun Pegawai</div>
                {{-- @if (session('regstatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('regstatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-2">
                    <label for="nama_pegawai" class="form-label-pink">Nama Pegawai</label>
                    <input id='nama_pegawai' name="nama_pegawai" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_pegawai') }}" placeholder="Nama Pegawai" required autofocus />
                    @error('nama_pegawai')
                        <label for="nama_pegawai" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="username_pegawai" class="form-label-pink">Username Pegawai</label>
                    <input id='username_pegawai' name="username_pegawai" type="text"
                        class="form-control form-control-pink" value="{{ old('username_pegawai') }}"
                        placeholder="Username Pegawai" required />
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
                <div class="mb-4">
                    <label for="password" class="form-label-pink">Password</label>
                    <input id="password" name="password" type="password" class="form-control form-control-pink"
                        placeholder="Password" required />
                    @error('password')
                        <label for="password" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-plus"></i> Tambah Akun
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('owner.kpegawai') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

