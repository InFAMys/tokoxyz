@forelse ($barang as $brg)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>
            @if ($brg->thumbnailPath())
                <img src="{{ asset('storage/' . $brg->thumbnailPath()) }}" alt="Thumbnail {{ $brg->nama_barang }}"
                    class="rounded" style="width: 64px; height: 64px; object-fit: cover;">
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ $brg->kode_barang }}</td>
        <td>{{ $brg->nama_barang }}</td>
        <td>Rp {{ number_format($brg->harga, 0, ',', '.') }}</td>
        <td>
            {{ !is_null($brg->berat) ? rtrim(rtrim(number_format((float) $brg->berat, 3, ',', '.'), '0'), ',') . ' kg' : '-' }}
        </td>
        <td>{{ $brg->stokReady() }}</td>
        @if ($brg->status == 'Disembunyikan')
            <td><span class="badge rounded-pill text-bg-warning">{{ $brg->status }}</span></td>
        @elseif($brg->status == 'Ditampilkan')
            <td><span class="badge rounded-pill text-bg-success">{{ $brg->status }}</span></td>
        @endif
        <td>
            <a href="{{ route('pegawai.detailbarang', $brg->id_barang) }}" class="btn btn-detail-outline btn-sm mx-2">
                <i class="fa-solid fa-circle-info"></i> Detail
            </a>
            <a href="{{ route('pegawai.ebarang', $brg->id_barang) }}" class="btn btn-edit-outline btn-sm mx-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center text-muted py-4">Tidak ada hasil</td>
    </tr>
@endforelse
