@extends('customer.layouts.app')

@section('title', 'Login - Toko XYZ')
@php
    $activ = 'login';
@endphp

@section('content')
    <!-- Session Status -->
    {{-- <x-auth-session-status class="mb-4" :status="session('status')" /> --}}

    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('login') }}" method="post">
                @csrf
                {{-- @if ($errors->any())
                    {{ implode('', $errors->all('<div>:message</div>')) }}
                @endif --}}
                <div class="auth-title">Login</div>
                @error('email')
                    <div class="alert alert-danger alert-dismissible text-danger" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <div class="mb-3">
                    <label for="email" class="form-label-pink">Email</label>
                    <input type="email" id="email" name="email" class="form-control form-control-pink"
                        placeholder="contoh@email.com" required autofocus autocomplete="username"
                        value="{{ old('email') }}" />
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label-pink">Password</label>
                    <input type="password" id="password" name="password" class="form-control form-control-pink"
                        placeholder="Masukkan Password" required autocomplete="current-password" />
                </div>
                <div class="my-3">
                    <input id="remember" type="checkbox" class="form-check-input mx-2" name="remember">
                    <label for="remember" class="form-label-rememberme">Tetap Masuk?</label>

                </div>
                {{-- <div class="mb-3 text-center">
                    <strong><x-input-error :messages="$errors->get('email')" class="mt-2" /></strong>
                    <strong><x-input-error :messages="$errors->get('password')" class="mt-2" /></strong>
                </div> --}}
                <button class="btn btn-pink w-100 mb-2" type="submit">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> LOGIN
                </button>
                {{-- <a class="btn btn-pink-outline w-100" href="{{ route('register') }}">
                    <i class="fa-solid fa-user-plus"></i> REGISTER
                </a> --}}
                <p class="text-center small mt-1">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="link-pink">Register</a>
                </p>
                {{-- <div class="mt-3 text-center">
                    <a href="{{ route('password.request') }}" class="link-pink" style="font-size: 0.85rem">Lupa
                        Password?</a>
                </div> --}}
            </form>
        </div>
    </div>
@endsection
