@forelse ($brn as $bn)
    <tr>
        <td>{{ $bn->id_brand }}</td>
        <td>
            @if ($bn->logo != '')
                <img src="{{ asset('storage/' . $bn->logo) }}" alt="Logo" style="width: 100px">
            @else
                <p>NO LOGO</p>
            @endif
        </td>
        <td>{{ $bn->nama_brand }}</td>
        <td>
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
@empty
    <tr>
        <td colspan="4" class="text-center text-muted py-4">Tidak ada hasil</td>
    </tr>
@endforelse
