@extends('customer.layouts.app')

@section('title', 'Edit Password - Toko XYZ')
@php
    $activ = 'profil';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <a class="btn btn-pink-outline mb-4" href="{{ route('profil') }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="auth-title">Edit Password</div>
            <form action="{{ route('password.update') }}" method="post">
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

