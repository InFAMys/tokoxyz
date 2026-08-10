@extends('customer.layouts.app')

@section('title', 'Register - Toko XYZ')
@php
    $activ = 'register';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('register') }}" method="post">
                <div class="auth-title">Register</div>
                <div class="mb-2">
                    <label for="nama" class="form-label-pink">Nama Lengkap</label>
                    <input id='nama' name="nama" type="text" class="form-control form-control-pink"
                        value="{{ old('nama') }}" placeholder="Nama lengkap Anda" maxlength="64" required autofocus />
                    @error('nama')
                        <label for="nama" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="username" class="form-label-pink">Username</label>
                    <input id='username' name="username" type="text" class="form-control form-control-pink"
                        value="{{ old('username') }}" placeholder="username" maxlength="15" required />
                    @error('username')
                        <label for="username" class="form-label-pink text-danger mt-2">
                            {{ $message }}
                        </label>
                    @else
                        <p class="form-label-rememberme mt-2 formhint">Panjang Maksimal 15 Karakter.
                        </p>
                        <p class="form-label-rememberme mt-2 formhint">Hanya huruf, angka, garis bawah (_), dan tanda hubung
                            (-)
                            yang diperbolehkan untuk Username.
                        </p>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="email" class="form-label-pink">Email</label>
                    <input id="email" name="email" type="email" class="form-control form-control-pink"
                        value="{{ old('email') }}" placeholder="contoh@email.com" required autocomplete="email" />
                    @error('email')
                        <label for="email" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="no_telp" class="form-label-pink">No. Telepon</label>
                    {{-- <input id="no_telp" name="no_telp" type="tel" class="form-control form-control-pink"
                        value="{{ old('no_telp') }}" placeholder="08xx-xxxx-xxxx" required /> --}}
                    <input id="no_telp" name="no_telp" type="tel" class="form-control form-control-pink"
                        value="{{ old('no_telp') }}" placeholder="08xx-xxxx-xxxx" inputmode="numeric"
                        pattern="[0-9\-]{9,15}" maxlength="12" oninput="this.value = this.value.replace(/[^0-9\-]/g, '')"
                        required />
                    @error('no_telp')
                        <label for="no_telp" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
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
                    <i class="fa-solid fa-user-plus"></i> REGISTER
                </button>
                <p class="text-center small mt-1">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="link-pink"">Login</a>
                </p>
            </form>
        </div>
    </div>
    </form>
@endsection
