<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-pink">
            <div class="modal-header">
                <h1 class="modal-title fs-4" id="logoutModalLabel">Log Out?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center my-4">
                Yakin ingin keluar dari akun ini?
            </div>
            <div class="modal-footer mx-auto">
                <button type="button" class="btn btn-green btn-sm" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> BATAL
                </button>
                <form method="POST" action="{{ $logoutRoute }}">
                    @csrf
                    <button type="submit" class="btn btn-delete btn-sm">
                        <i class="fa-solid fa-right-from-bracket"></i> LOG OUT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>