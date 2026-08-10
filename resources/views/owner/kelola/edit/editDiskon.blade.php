@extends('owner.layouts.app')

@section('title', 'Edit Diskon - Toko XYZ')
@php
    $activ = 'diskon';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <form action="{{ route('owner.eddiskon', $diskon->id_diskon) }}" method="post">
                @csrf
                @method('PUT')
                <div class="auth-titleA">Edit Diskon</div>
                <div class="auth-subtitleA">{{ $diskon->nama_diskon }}</div>
                <div class="auth-pgw-name my-3">{{ $diskon->nama_pegawai }}</div>
                {{-- @if (session('estatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('estatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}
                <div class="mb-2">
                    <label for="nama_diskon" class="form-label-pink">Nama Diskon</label>
                    <input id='nama_diskon' name="nama_diskon" type="text" class="form-control form-control-pink"
                        value="{{ old('nama_diskon', $diskon->nama_diskon) }}" placeholder="Nama Diskon" required />
                    @error('nama_diskon')
                        <label for="nama_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="jumlah_diskon" class="form-label-pink">Jumlah Diskon</label>
                    <div class="input-group mb-3">
                        <input id='jumlah_diskon' type="text" inputmode="decimal" maxlength="5"
                            pattern="[0-9]*\.?[0-9]*" name="jumlah_diskon" class="form-control form-control-pink"
                            aria-label="Jumlah Diskon" aria-describedby="jumlah-diskon"
                            value="{{ old('jumlah_diskon', $diskon->jumlah_diskon) }}" placeholder="Jumlah Diskon"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                        <span class="input-group-text" id="jumlah-diskon">%</span>
                    </div>
                    @error('jumlah_diskon')
                        <label for="jumlah_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="kode_diskon" class="form-label-pink">Kode Diskon</label>
                    <input id='kode_diskon' name="kode_diskon" type="text" class="form-control form-control-pink"
                        value="{{ old('kode_diskon', $diskon->kode_diskon) }}" placeholder="Kode Diskon" maxlength="10"
                        style="text-transform: uppercase;" required />
                    @error('kode_diskon')
                        <label for="kode_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="mulai_diskon" class="form-label-pink">Mulai Diskon</label>
                    <input id='mulai_diskon' name="mulai_diskon" type="datetime-local"
                        class="form-control form-control-pink" value="{{ old('mulai_diskon', $diskon->mulai_diskon) }}"
                        placeholder="Waktu Mulai Diskon" required />
                    @error('mulai_diskon')
                        <label for="mulai_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="akhir_diskon" class="form-label-pink">Akhir Diskon</label>
                    <input id='akhir_diskon' name="akhir_diskon" type="datetime-local"
                        class="form-control form-control-pink" value="{{ old('akhir_diskon', $diskon->akhir_diskon) }}"
                        placeholder="Nama Diskon" required />
                    @error('akhir_diskon')
                        <label for="akhir_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="status_diskon" class="form-label-pink">Status Diskon</label>
                    <select id="status_diskon" name="status_diskon" class="form-select form-control-pink" required>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}" @selected(old('status_diskon', $diskon->status_diskon) === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_diskon')
                        <label for="status_diskon" class="form-label-pink text-danger">
                            {{ $message }}
                        </label>
                    @enderror
                </div>
                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Diskon
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('owner.kdiskon') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection

