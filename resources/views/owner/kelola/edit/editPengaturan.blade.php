@extends('owner.layouts.app')

@section('title', 'Profil Perusahaan - Toko XYZ')
@php
    $activ = 'pengaturan';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('owner.updpengaturan') }}" method="post" data-confirm="Simpan Pengaturan?">
                @csrf
                @method('PUT')
                <div class="auth-titleA">Profil Perusahaan</div>
                <div class="auth-subtitleA">Informasi yang tampil di halaman utama toko</div>

                @foreach (\App\Models\Pengaturan::KEYS as $key => $label)
                    @php $textarea = in_array($key, ['deskripsi', 'alamat']); @endphp
                    <div class="mb-2">
                        <label for="{{ $key }}" class="form-label-pink">{{ $label }}</label>
                        @if ($textarea)
                            <textarea id="{{ $key }}" name="{{ $key }}" rows="3"
                                class="form-control form-control-pink"
                                placeholder="{{ $label }}">{{ old($key, $pengaturan[$key] ?? '') }}</textarea>
                        @else
                            <input id="{{ $key }}" name="{{ $key }}" type="text"
                                class="form-control form-control-pink"
                                value="{{ old($key, $pengaturan[$key] ?? '') }}"
                                placeholder="{{ $label }}" />
                        @endif
                        @error($key)
                            <label for="{{ $key }}" class="form-label-pink text-danger">
                                {{ $message }}
                            </label>
                        @enderror
                    </div>
                @endforeach

                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('owner.dashboard') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection
