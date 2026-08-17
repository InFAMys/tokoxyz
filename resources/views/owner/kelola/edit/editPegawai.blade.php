@extends('owner.layouts.app')

@section('title', 'Edit Akun Pegawai - Toko XYZ')
@php
    $activ = 'pegawai';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('owner.edpegawai', $pgw->id_pegawai) }}" method="post">
                @csrf
                @method('PUT')
                <div class="auth-titleA">Edit Akun Pegawai</div>
                <div class="auth-pgw-name my-3">{{ $pgw->nama_pegawai }}</div>
                
                <div class="mb-2">
                    <label for="nama_pegawai" class="form-label-pink">Nama Pegawai</label>
                    <input id='nama_pegawai' name="nama_pegawai" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_pegawai', $pgw->nama_pegawai) }}" placeholder="Nama Pegawai" required />
                    @error('nama_pegawai')
                        <label for="nama_pegawai" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="username_pegawai" class="form-label-pink">Username Pegawai</label>
                    <input id='username_pegawai' name="username_pegawai" type="text"
                        class="form-control form-control-pink" value="{{ old('username_pegawai', $pgw->username_pegawai) }}"
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
                        placeholder="Password" />
                    @error('password')
                        <label for="password" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Akun
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('owner.kpegawai') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

