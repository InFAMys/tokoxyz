@extends('customer.layouts.app')

@php
    $activ = 'profil';
    $isEdit = isset($alamat);
@endphp


@section('title', $isEdit ? 'Edit Alamat - Toko XYZ' : 'Tambah Alamat - Toko XYZ')

@section('content')
    <div class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <p class="text-pink fw-semibold mb-1">Alamat Pengiriman</p>
                <h1 class="page-title mb-0">{{ $isEdit ? 'Edit Alamat' : 'Tambah Alamat' }}</h1>
            </div>
            <a href="{{ route('alamat.index') }}" class="btn btn-pink-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ $isEdit ? route('alamat.update', $alamat->id_alamat) : route('alamat.store') }}" method="post">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-pink p-4 mb-4">
                        <div class="form-label-pink mb-3"><i class="fa-solid fa-user"></i> Data Penerima</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_alamat" class="form-label-pink">Label Alamat</label>
                                <input id="nama_alamat" name="nama_alamat" type="text"
                                    class="form-control form-control-pink"
                                    value="{{ old('nama_alamat', $isEdit ? $alamat->nama_alamat : '') }}"
                                    placeholder="contoh: Rumah, Kantor" maxlength="50" required>
                                @error('nama_alamat')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nama_penerima" class="form-label-pink">Nama Penerima</label>
                                <input id="nama_penerima" name="nama_penerima" type="text"
                                    class="form-control form-control-pink"
                                    value="{{ old('nama_penerima', $isEdit ? $alamat->nama_penerima : '') }}"
                                    placeholder="Nama Lengkap" maxlength="64" required>
                                @error('nama_penerima')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telp_penerima" class="form-label-pink">No. Telepon</label>
                                <input id="telp_penerima" name="telp_penerima" type="tel"
                                    class="form-control form-control-pink"
                                    value="{{ old('telp_penerima', $isEdit ? $alamat->telp_penerima : '') }}"
                                    placeholder="08xx-xxxx-xxxx" inputmode="numeric" pattern="[0-9\-]{9,15}" maxlength="12"
                                    oninput="this.value = this.value.replace(/[^0-9\-]/g, '')" required>
                                @error('telp_penerima')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-pink p-4 mb-4">
                        <div class="form-label-pink mb-3"><i class="fa-solid fa-location-dot"></i> Wilayah (Klikresi)</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="id_provinsi" class="form-label-pink">Provinsi</label>
                                <select id="id_provinsi" name="id_provinsi" class="form-select form-control-pink" required
                                    data-cities-url="{{ route('alamat.cities', ':id') }}">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province['id'] }}" @selected(old('id_provinsi', $isEdit ? $alamat->id_provinsi : '') == $province['id'])>
                                            {{ $province['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="provinsi"
                                    value="{{ old('provinsi', $isEdit ? $alamat->provinsi : '') }}">
                                @error('provinsi')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="id_kota" class="form-label-pink">Kota/Kabupaten</label>
                                <select id="id_kota" name="id_kota" class="form-select form-control-pink" required
                                    data-saved="{{ old('id_kota', $isEdit ? $alamat->id_kota : '') }}"
                                    data-districts-url="{{ route('alamat.districts', ':id') }}">
                                    <option value="">Pilih Kota</option>
                                </select>
                                <input type="hidden" name="kota"
                                    value="{{ old('kota', $isEdit ? $alamat->kota : '') }}">
                                @error('kota')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="id_kecamatan" class="form-label-pink">Kecamatan</label>
                                <select id="id_kecamatan" name="id_kecamatan" class="form-select form-control-pink" required
                                    data-saved="{{ old('id_kecamatan', $isEdit ? $alamat->id_kecamatan : '') }}">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="kecamatan"
                                    value="{{ old('kecamatan', $isEdit ? $alamat->kecamatan : '') }}">
                                @error('kecamatan')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="kelurahan" class="form-label-pink">Kelurahan</label>
                                <input id="kelurahan" name="kelurahan" type="text" class="form-control form-control-pink"
                                    value="{{ old('kelurahan', $isEdit ? $alamat->kelurahan : '') }}"
                                    placeholder="Kelurahan / Desa" maxlength="64" required>
                                @error('kelurahan')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="kode_pos" class="form-label-pink">Kode Pos</label>
                                <input id="kode_pos" name="kode_pos" type="text"
                                    class="form-control form-control-pink"
                                    value="{{ old('kode_pos', $isEdit ? $alamat->kode_pos : '') }}"
                                    placeholder="contoh: 61174" maxlength="10" required>
                                @error('kode_pos')
                                    <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-pink p-4">
                        <div class="form-label-pink mb-3"><i class="fa-solid fa-house"></i> Detail Alamat</div>
                        <label for="detail_alamat" class="form-label-pink">Alamat Lengkap</label>
                        <textarea id="detail_alamat" name="detail_alamat" rows="3" class="form-control form-control-pink"
                            placeholder="Nama jalan, gang, nomor rumah, patokan" required>{{ old('detail_alamat', $isEdit ? $alamat->detail_alamat : '') }}</textarea>
                        @error('detail_alamat')
                            <div class="form-label-pink text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-box">
                        <div class="form-label-pink mb-2">Ringkasan</div>
                        @if ($isEdit)
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Label</span>
                                <span>{{ $alamat->nama_alamat }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Penerima</span>
                                <span>{{ $alamat->nama_penerima }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Telepon</span>
                                <span>{{ $alamat->telp_penerima }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Wilayah</span>
                                <span>{{ $alamat->provinsi }}</span>
                            </div>
                        @else
                            <p class="text-muted mb-1">Lengkapi data di samping untuk menyimpan alamat pengiriman.</p>
                        @endif
                        <hr class="my-3">
                        <button type="submit" class="btn btn-pink w-100">
                            <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Alamat' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
