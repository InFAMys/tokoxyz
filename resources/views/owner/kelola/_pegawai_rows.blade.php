@forelse ($pgw as $pg)
    <tr>
        <td>{{ $pg->id_pegawai }}</td>
        <td>{{ $pg->nama_pegawai }}</td>
        <td>{{ $pg->username_pegawai }}</td>
        <td class="d-flex gap-3">
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
@empty
    <tr>
        <td colspan="4" class="text-center text-muted py-4">Tidak ada hasil</td>
    </tr>
@endforelse
