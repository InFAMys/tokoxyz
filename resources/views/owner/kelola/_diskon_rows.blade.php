@if ($diskon->isEmpty())
    <div class="text-center py-4 text-muted">Tidak ada hasil</div>
@else
    <div class="table-responsive">
        <table class="table table-pink table-borderless text-center mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Diskon</th>
                    <th>Kode Diskon</th>
                    <th>Jumlah Diskon</th>
                    <th>Mulai-Akhir Diskon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($diskon as $ds)
                    <tr>
                        <td>{{ $diskon->firstItem() + $loop->index }}</td>
                        <td>{{ $ds->nama_diskon }}</td>
                        <td>{{ $ds->kode_diskon }}</td>
                        <td class="text-center">{{ (int) $ds->jumlah_diskon }}%</td>
                        <td>
                            <span class="badge rounded-pill text-bg-success"
                                style="font-size: 0.7rem">{{ \Carbon\Carbon::parse($ds->mulai_diskon)->format('d-m-Y H:i') }}</span>
                            -
                            <span class="badge rounded-pill text-bg-warning"
                                style="font-size: 0.7rem">{{ \Carbon\Carbon::parse($ds->akhir_diskon)->format('d-m-Y H:i') }}</span>
                        </td>
                        <td>
                            @php
                                $now = now();
                                $expired = $ds->status_diskon == 'aktif' && ($now < $ds->mulai_diskon || $now > $ds->akhir_diskon);
                            @endphp
                            @if ($expired)
                                <span class="badge rounded-pill text-bg-danger">Kadaluarsa</span>
                            @elseif ($ds->status_diskon == 'aktif')
                                <span class="badge rounded-pill text-bg-primary">Aktif</span>
                            @elseif ($ds->status_diskon == 'nonaktif')
                                <span class="badge rounded-pill text-bg-secondary">Nonaktif</span>
                            @elseif ($ds->status_diskon == 'kadaluarsa')
                                <span class="badge rounded-pill text-bg-danger">Kadaluarsa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('owner.ediskon', $ds->id_diskon) }}" class="btn btn-edit-outline btn-sm me-1">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <button class="btn btn-delete-outline btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal-{{ $ds->id_diskon }}">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                            <div class="modal fade" id="deleteModal-{{ $ds->id_diskon }}" tabindex="-1"
                                aria-labelledby="deleteModal-{{ $ds->id_diskon }}Label" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-pink">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-4" id="deleteModal-{{ $ds->id_diskon }}Label">
                                                Hapus Kategori ?
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center my-4">
                                            Hapus Kategori {{ $ds->nama_diskon }} ?
                                        </div>
                                        <div class="modal-footer mx-auto">
                                            <form action="{{ route('owner.deldiskon', $ds->id_diskon) }}" method="post">
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
    @if ($diskon->hasPages())
        <nav class="mt-3">{{ $diskon->links() }}</nav>
    @endif
@endif
