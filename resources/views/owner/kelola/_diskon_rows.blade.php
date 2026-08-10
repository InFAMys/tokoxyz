@forelse ($diskon as $ds)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $ds->nama_diskon }}</td>
        <td>{{ $ds->kode_diskon }}</td>
        <td class="text-center">{{ $ds->jumlah_diskon }}%</td>
        <td>
            <span class="badge rounded-pill text-bg-success"
                style="font-size: 0.7rem">{{ $ds->mulai_diskon }}</span>
            -
            <span class="badge rounded-pill text-bg-warning"
                style="font-size: 0.7rem">{{ $ds->akhir_diskon }}</span>
        </td>
        <td>
            @if ($ds->status_diskon == 'aktif')
                <span class="badge rounded-pill text-bg-primary">Aktif</span>
            @elseif ($ds->status_diskon == 'nonaktif')
                <span class="badge rounded-pill text-bg-secondary">Nonaktif</span>
            @endif
        </td>
        <td>
            <a href="{{ route('owner.ediskon', $ds->id_diskon) }}"
                class="btn btn-edit-outline btn-sm me-1">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                data-bs-target="#deleteModal-{{ $ds->id_diskon }}">
                <i class="fa-solid fa-trash"></i> Hapus
            </button>
            <div class="modal fade" id="deleteModal-{{ $ds->id_diskon }}" tabindex="-1"
                aria-labelledby="deleteModal-{{ $ds->id_diskon }}Label" aria-hidden="true"
                data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-pink">
                        <div class="modal-header">
                            <h1 class="modal-title fs-4"
                                id="deleteModal-{{ $ds->id_diskon }}Label">
                                Hapus Kategori ?
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center my-4">
                            Hapus Kategori {{ $ds->nama_diskon }} ?
                        </div>
                        <div class="modal-footer mx-auto">
                            <form action="{{ route('owner.deldiskon', $ds->id_diskon) }}"
                                method="post">
                                @csrf
                                <button class="btn btn-delete btn-sm" type="submit">
                                    <i class="fa-solid fa-trash"></i> HAPUS
                                </button>
                            </form>
                            <button class="btn btn-green btn-sm" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark"></i> BATAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">Tidak ada hasil</td>
    </tr>
@endforelse
