@extends('pegawai.layouts.app')

@section('title', 'Edit Barang: ' . $barang->nama_barang . ' - Toko XYZ')

@php
    $activ = 'barang';
@endphp

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 720px;">
            <form action="{{ route('pegawai.ubarang', $barang->id_barang) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="auth-title">Edit Barang</div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="id_brand" class="form-label-pink">Brand</label>
                        <select id="id_brand" name="id_brand" class="form-select form-control-pink" required>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id_brand }}" @selected(old('id_brand', $barang->id_brand) == $brand->id_brand)>{{ $brand->nama_brand }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_brand')
                            <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="id_kategori" class="form-label-pink">Kategori</label>
                        <select id="id_kategori" name="id_kategori" class="form-select form-control-pink" required>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id_kategori }}" @selected(old('id_kategori', $barang->id_kategori) == $kategori->id_kategori)>
                                    {{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="mb-2 mt-2">
                    <label for="kode_barang" class="form-label-pink">Kode Barang</label>
                    <input id="kode_barang" name="kode_barang" type="text" maxlength="15"
                        class="form-control form-control-pink" value="{{ old('kode_barang', $barang->kode_barang) }}"
                        required>
                    @error('kode_barang')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="nama_barang" class="form-label-pink">Nama Barang</label>
                    <input id="nama_barang" name="nama_barang" type="text" maxlength="32"
                        class="form-control form-control-pink" value="{{ old('nama_barang', $barang->nama_barang) }}"
                        required>
                    @error('nama_barang')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="deskripsi" class="form-label-pink">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control form-control-pink" required>{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-2">
                    <label class="form-label-pink">Thumbnail Saat Ini</label>
                    @if ($barang->thumbnailPath())
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $barang->thumbnailPath()) }}"
                                alt="Thumbnail {{ $barang->nama_barang }}" class="rounded"
                                style="width: 160px; height: 160px; object-fit: cover;">
                        </div>
                    @else
                        <p class="form-label-rememberme mb-2">Belum ada thumbnail.</p>
                    @endif
                    <label for="thumbnail" class="form-label-pink">Ganti Thumbnail</label>
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png"
                        class="form-control form-control-pink">
                    <p class="form-label-rememberme mt-2 mb-0">Kosongkan jika tidak ingin mengganti thumbnail.</p>
                    @error('thumbnail')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-2">
                    <label class="form-label-pink">Foto Saat Ini</label>
                    <p class="form-label-rememberme mb-2">Centang foto yang ingin dihapus.</p>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($barang->foto as $foto)
                            <label class="position-relative" style="cursor: pointer;">
                                <img src="{{ asset('storage/' . $foto) }}"
                                    alt="Foto {{ $loop->iteration }} {{ $barang->nama_barang }}" class="rounded"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <span class="d-block mt-1 form-label-rememberme">
                                    <input type="checkbox" name="hapus_foto[]" value="{{ $foto }}"
                                        @checked(in_array($foto, old('hapus_foto', [])))>
                                    Hapus
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('hapus_foto')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                    @error('hapus_foto.*')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="foto" class="form-label-pink">Tambah Foto Baru</label>
                    <input id="foto" name="foto[]" type="file" accept="image/jpeg,image/png"
                        class="form-control form-control-pink" multiple>
                    <p class="form-label-rememberme mt-2 mb-0">Maksimal 5 foto total, masing-masing maksimal 5 MB.</p>
                    @error('foto')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                    @error('foto.*')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="harga" class="form-label-pink">Harga</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="harga-addon1">Rp</span>
                        <input type="text" inputmode="numeric" autocomplete="off" class="form-control form-control-pink"
                            name="harga" placeholder="Harga"
                            aria-label="Harga" aria-describedby="harga-addon1"
                            value="{{ old('harga', $barang->harga) }}" required>
                    </div>
                    @error('harga')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="berat" class="form-label-pink">Berat</label>
                    <div class="input-group mb-3">
                        <input id="berat" type="text" class="form-control form-control-pink" name="berat"
                            placeholder="0,5" aria-label="Berat" aria-describedby="berat-addon1" inputmode="decimal"
                            value="{{ str_replace('.', ',', old('berat', $barang->berat)) }}">
                        <span class="input-group-text" id="berat-addon1">kg</span>
                    </div>
                    @error('berat')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label for="status" class="form-label-pink">Status</label>
                        <select id="status" name="status" class="form-select form-control-pink" required>
                            <option value="Disembunyikan" @selected(old('status', $barang->status) === 'Disembunyikan')>Disembunyikan</option>
                            <option value="Ditampilkan" @selected(old('status', $barang->status) === 'Ditampilkan')>Ditampilkan</option>
                        </select>
                        @error('status')
                            <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="preorder" class="form-label-pink">Preorder</label>
                        <select id="preorder" name="preorder" class="form-select form-control-pink" required>
                            <option value="Tidak Tersedia" @selected(old('preorder', $barang->preorder) === 'Tidak Tersedia')>Tidak Tersedia</option>
                            <option value="Tersedia" @selected(old('preorder', $barang->preorder) === 'Tersedia')>Tersedia</option>
                        </select>
                        @error('preorder')
                            <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="estimasi_preorder" class="form-label-pink">Estimasi Preorder (hari)</label>
                    <input id="estimasi_preorder" name="estimasi_preorder" type="number" min="1" step="1"
                        class="form-control form-control-pink"
                        value="{{ old('estimasi_preorder', $barang->estimasi_preorder) }}">
                    @error('estimasi_preorder')
                        <label class="form-label-pink text-danger mt-2">{{ $message }}</label>
                    @enderror
                </div>

                <button type="submit" class="btn btn-pink w-100 mb-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a class="btn btn-pink-outline w-100" href="{{ route('pegawai.detailbarang', $barang->id_barang) }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
@endsection
