@extends('owner.layouts.app')

@section('title', 'Edit Akun Owner - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('owner.update.username') }}" method="post">
                @csrf
                @method('PUT')
                <div class="auth-title">Edit Profil</div>
                
                <div class="mb-4">
                    <label for="username" class="form-label-pink">Username</label>
                    <input id='username' name="username" type="text" class="form-control form-control-pink"
                        value="" placeholder="{{ old('username', $owner->username) }}" required />
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
            <hr>
            <form action="{{ route('owner.update.password') }}" method="post">
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
                        placeholder="Password" required />
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

