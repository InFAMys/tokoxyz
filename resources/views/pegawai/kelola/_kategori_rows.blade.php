@if ($kategori->isEmpty())
    <div class="text-center py-4 text-muted">Tidak ada hasil</div>
@else
    <div class="table-responsive mobile-card-responsive">
        <table class="table table-pink table-borderless mobile-card-table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategori as $kat)
                    <tr>
                        <td data-label="ID">{{ $kat->id_kategori }}</td>
                        <td data-label="Nama Kategori">{{ $kat->nama_kategori }}</td>
                        <td data-label="Aksi" class="mobile-card-actions">
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
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($kategori->hasPages())
        <nav class="mt-3">{{ $kategori->links() }}</nav>
    @endif
@endif
