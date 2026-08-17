@if ($brn->isEmpty())
    <div class="text-center py-4 text-muted">Tidak ada hasil</div>
@else
    <div class="table-responsive mobile-card-responsive">
        <table class="table table-pink table-borderless mobile-card-table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo Brand</th>
                    <th>Nama Brand</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brn as $bn)
                    <tr>
                        <td data-label="ID">{{ $bn->id_brand }}</td>
                        <td data-label="Logo Brand">
                            @if ($bn->logo != '')
                                <img src="{{ asset('storage/' . $bn->logo) }}" alt="Logo" style="width: 100px">
                            @else
                                <p>NO LOGO</p>
                            @endif
                        </td>
                        <td data-label="Nama Brand">{{ $bn->nama_brand }}</td>
                        <td data-label="Aksi" class="mobile-card-actions">
                            <a href="{{ route('pegawai.ebrand', $bn->id_brand) }}"
                                class="btn btn-edit-outline btn-sm me-1">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal-{{ $bn->id_brand }}">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                            <div class="modal fade" id="deleteModal-{{ $bn->id_brand }}" tabindex="-1"
                                aria-labelledby="deleteModal-{{ $bn->id_brand }}Label" aria-hidden="true"
                                data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-pink">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-4" id="deleteModal-{{ $bn->id_brand }}Label">
                                                Hapus Brand ?
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center my-4">
                                            Hapus Brand {{ $bn->nama_brand }} ?
                                        </div>
                                        <div class="modal-footer mx-auto">
                                            <form action="{{ route('pegawai.delbrand', $bn->id_brand) }}" method="post">
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
    @if ($brn->hasPages())
        <nav class="mt-3">{{ $brn->links() }}</nav>
    @endif
@endif
