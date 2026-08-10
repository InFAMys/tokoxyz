<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Pegawai - Toko XYZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    @vite('resources/css/style.css')
    <script src="https://kit.fontawesome.com/9f2b17dc69.js" crossorigin="anonymous"></script>
</head>

    <body>
        @include('components.toasts')
    <!-- Navbar -->
    <nav class="navbar navbar-pink d-flex justify-content-between align-items-center">
        <span class="brand"><i class="fa-solid fa-shop"></i> Toko XYZ</span>
        <div class="d-flex gap-1"></div>
    </nav>
    <form action="{{ route('pegawai.login') }}" method="post">
        @csrf
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-titleA">Login</div>
                <div class="auth-subtitleA">Pegawai</div>
                @error('username_pegawai')
                    <div class="alert alert-danger alert-dismissible text-danger" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror
                <div class="mb-3">
                    <label for="username_pegawai" class="form-label-pink">Username</label>
                    <input id="username_pegawai" name="username_pegawai" type="text"
                        class="form-control form-control-pink" placeholder="Masukkan username"
                        value="{{ old('username_pegawai') }}" required />
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label-pink">Password</label>
                    <input id="password" name="password" type="password" class="form-control form-control-pink"
                        placeholder="Masukkan password" required />
                </div>
                <button class="btn btn-pink w-100" type="submit">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> LOGIN
                </button>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>
