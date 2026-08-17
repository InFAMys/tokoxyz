@if ($pgw->isEmpty())
    <div class="text-center py-4 text-muted">Tidak ada hasil</div>
@else
    <div class="table-responsive mobile-card-responsive">
        <table class="table table-pink table-borderless mobile-card-table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pegawai</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pgw as $pg)
                    <tr>
                        <td data-label="ID">{{ $pg->id_pegawai }}</td>
                        <td data-label="Nama Pegawai">{{ $pg->nama_pegawai }}</td>
                        <td data-label="Username">{{ $pg->username_pegawai }}</td>
                        <td data-label="Aksi" class="d-flex gap-3 mobile-stack-actions mobile-card-actions">
                            <a href="{{ route('owner.epegawai', $pg->id_pegawai) }}"
                                class="btn btn-edit-outline btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal-{{ $pg->id_pegawai }}">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                            <div class="modal fade" id="deleteModal-{{ $pg->id_pegawai }}" tabindex="-1"
                                aria-labelledby="deleteModal-{{ $pg->id_pegawai }}Label" aria-hidden="true"
                                data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-pink">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-4" id="deleteModal-{{ $pg->id_pegawai }}Label">
                                                Hapus Akun Pegawai ?
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center my-4">
                                            Hapus Akun Pegawai {{ $pg->nama_pegawai }} ?
                                        </div>
                                        <div class="modal-footer mx-auto">
                                            <form action="{{ route('owner.delpegawai', $pg->id_pegawai) }}"
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
    @if ($pgw->hasPages())
        <nav class="mt-3">{{ $pgw->links() }}</nav>
    @endif
@endif
