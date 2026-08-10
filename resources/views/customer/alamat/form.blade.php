@extends('customer.layouts.app')

@section('title', 'Alamat - Toko XYZ')
@php
    $activ = 'profil';
    $isEdit = isset($alamat);
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <a class="btn btn-pink-outline mb-4" href="{{ route('alamat.index') }}">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="auth-title">{{ $isEdit ? 'Edit Alamat' : 'Tambah Alamat' }}</div>
            <form action="{{ $isEdit ? route('alamat.update', $alamat->id_alamat) : route('alamat.store') }}" method="post">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                @php
                    $fields = [
                        'nama_alamat' => 'Label Alamat (contoh: Rumah, Kantor)',
                        'nama_penerima' => 'Nama Penerima',
                        'telp_penerima' => 'No. Telepon Penerima',
                        'detail_alamat' => 'Detail Alamat',
                        'kecamatan' => 'Kecamatan',
                        'kelurahan' => 'Kelurahan',
                        'kota' => 'Kota',
                        'provinsi' => 'Provinsi',
                        'kode_pos' => 'Kode Pos',
                    ];
                    $types = [
                        'telp_penerima' => 'tel',
                        'kode_pos' => 'text',
                    ];
                @endphp

                @foreach ($fields as $key => $label)
                    <div class="mb-4">
                        <label for="{{ $key }}" class="form-label-pink">{{ $label }}</label>
                        @if ($key == 'detail_alamat')
                            <textarea id="{{ $key }}" name="{{ $key }}" rows="3" class="form-control form-control-pink"
                                placeholder="{{ $label }}" required>{{ old($key, $isEdit ? $alamat->{$key} : '') }}</textarea>
                        @elseif ($key == 'telp_penerima')
                            <input id="{{ $key }}" name="{{ $key }}" type="tel"
                                class="form-control form-control-pink"
                                value="{{ old($key, $isEdit ? $alamat->{$key} : '') }}" placeholder="08xx-xxxx-xxxx"
                                inputmode="numeric" pattern="[0-9\-]{9,15}" maxlength="12"
                                oninput="this.value = this.value.replace(/[^0-9\-]/g, '')" required />
                        @else
                            <input id="{{ $key }}" name="{{ $key }}"
                                type="{{ $types[$key] ?? 'text' }}" class="form-control form-control-pink"
                                value="{{ old($key, $isEdit ? $alamat->{$key} : '') }}" placeholder="{{ $label }}"
                                maxlength="{{ $key === 'kode_pos' ? 10 : 255 }}" required />
                        @endif
                        @error($key)
                            <label for="{{ $key }}" class="form-label-pink text-danger mt-2">
                                {{ $message }}
                            </label>
                        @enderror
                    </div>
                @endforeach

                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-location-dot"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Alamat' }}
                </button>
            </form>
        </div>
    </div>
@endsection
