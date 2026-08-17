@if ($barang->isEmpty())
    <div class="text-center py-4 text-muted">Tidak ada hasil</div>
@else
    <div class="table-responsive mobile-card-responsive">
        <table class="table table-pink table-borderless mobile-card-table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Thumbnail</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Berat</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $brg)
                    <tr>
                        <td data-label="No">{{ $barang->firstItem() + $loop->index }}</td>
                        <td data-label="Thumbnail">
                            @if ($brg->thumbnailPath())
                                <img src="{{ asset('storage/' . $brg->thumbnailPath()) }}" alt="Thumbnail {{ $brg->nama_barang }}"
                                    class="rounded" style="width: 64px; height: 64px; object-fit: cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td data-label="Kode Barang">{{ $brg->kode_barang }}</td>
                        <td data-label="Nama Barang">{{ $brg->nama_barang }}</td>
                        <td data-label="Harga">Rp {{ number_format($brg->harga, 0, ',', '.') }}</td>
                        <td data-label="Berat">
                            {{ !is_null($brg->berat) ? rtrim(rtrim(number_format((float) $brg->berat, 1, ',', '.'), '0'), ',') . ' kg' : '-' }}
                        </td>
                        <td data-label="Stok">{{ $brg->stokReady() }}</td>
                        @if ($brg->status == 'Disembunyikan')
                            <td data-label="Status"><span class="badge rounded-pill text-bg-warning">{{ $brg->status }}</span></td>
                        @elseif($brg->status == 'Ditampilkan')
                            <td data-label="Status"><span class="badge rounded-pill text-bg-success">{{ $brg->status }}</span></td>
                        @endif
                        <td data-label="Aksi" class="mobile-card-actions">
                            <a href="{{ route('pegawai.detailbarang', $brg->id_barang) }}" class="btn btn-detail-outline btn-sm mx-2">
                                <i class="fa-solid fa-circle-info"></i> Detail
                            </a>
                            <a href="{{ route('pegawai.ebarang', $brg->id_barang) }}" class="btn btn-edit-outline btn-sm mx-2">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($barang->hasPages())
        <nav class="mt-3">{{ $barang->links() }}</nav>
    @endif
@endif
