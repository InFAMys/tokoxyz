@forelse ($kategori as $kat)
    <tr>
        <td>{{ $kat->id_kategori }}</td>
        <td>{{ $kat->nama_kategori }}</td>
        <td>
            <a href="{{ route('pegawai.ekategori', $kat->id_kategori) }}"
                class="btn btn-edit-outline btn-sm me-1">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                data-bs-target="#deleteModal-{{ $kat->id_kategori }}">
                <i class="fa-solid fa-trash"></i> Hapus
            </button>
            <div class="modal fade" id="deleteModal-{{ $kat->id_kategori }}" tabindex="-1"
                aria-labelledby="deleteModal-{{ $kat->id_kategori }}Label" aria-hidden="true"
                data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-pink">
                        <div class="modal-header">
                            <h1 class="modal-title fs-4"
                                id="deleteModal-{{ $kat->id_kategori }}Label">
                                Hapus Kategori ?
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center my-4">
                            Hapus Kategori {{ $kat->nama_kategori }} ?
                        </div>
                        <div class="modal-footer mx-auto">
                            <form action="{{ route('pegawai.delkategori', $kat->id_kategori) }}"
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
        <td colspan="3" class="text-center text-muted py-4">Tidak ada hasil</td>
    </tr>
@endforelse
