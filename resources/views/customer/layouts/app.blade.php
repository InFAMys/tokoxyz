<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Laravel App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    @vite('resources/css/style.css')
    @vite('resources/css/fontawesome/css/all.css')
</head>

<body>
    @include('components.toasts')
    <nav class="navbar sticky-top navbar-expand-lg">
        <div class="container-fluid">
            <span class="brand"><i class="fa-solid fa-shop"></i> {{ config('app.name', 'Laravel') }} </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                @if (!auth('customer')->check())
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item mx-1">
                                @if ($activ == 'home')
                                    <a href="{{ route('home') }}" class="nav-link active-link"> <i
                                            class="fa-solid fa-house"></i>
                                        Home</a>
                                @else
                                    <a href="{{ route('home') }}" class="nav-link"> <i class="fa-solid fa-house"></i>
                                        Home</a>
                                @endif
                            </li>
                            <li class="nav-item mx-1">
                                @if ($activ == 'register')
                                    <a href="{{ route('register') }}" class="nav-link active-link">
                                        <i class="fa-solid fa-user-plus"></i> Register</a>
                                @else
                                    <a href="{{ route('register') }}" class="nav-link">
                                        <i class="fa-solid fa-user-plus"></i> Register</a>
                                @endif
                            </li>
                            <li class="nav-item mx-1">
                                @if ($activ == 'login')
                                    <a href="{{ route('login') }}" class="nav-link active-link">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login</a>
                                @else
                                    <a href="{{ route('login') }}" class="nav-link">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login</a>
                                @endif
                            </li>
                        </ul>
                    @elseif (auth('customer')->check())
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item mx-1">
                                @if ($activ == 'home')
                                    <a href="{{ route('home') }}" class="nav-link active-link"> <i
                                            class="fa-solid fa-house"></i>
                                        Home</a>
                                @else
                                    <a href="{{ route('home') }}" class="nav-link"> <i class="fa-solid fa-house"></i>
                                        Home</a>
                                @endif
                            </li>
                            <li class="nav-item mx-1">
                                @if ($activ == 'keranjang')
                                    <a href="{{ route('keranjang.index') }}" class="nav-link active-link"><i
                                            class="fa-solid fa-shopping-cart"></i> Keranjang</a>
                                @else
                                    <a href="{{ route('keranjang.index') }}" class="nav-link"><i class="fa-solid fa-shopping-cart"></i>
                                        Keranjang</a>
                                @endif

                            </li>
                            <li class="nav-item mx-1">
                                @if ($activ == 'pesanan')
                                    <a href="{{ route('checkout.history') }}" class="nav-link active-link">
                                        <i class="fa-solid fa-receipt"></i> Pesanan
                                    </a>
                                @else
                                    <a href="{{ route('checkout.history') }}" class="nav-link">
                                        <i class="fa-solid fa-receipt"></i> Pesanan
                                    </a>
                                @endif

                            </li>
                            <li class="nav-item dropdown mx-1">
                                @if ($activ == 'profil')
                                    <a href="#" class="dropdown-toggle nav-link active-link" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-circle-user"></i>
                                        {{ Str::limit(auth('customer')->user()->nama, 10) }}
                                    </a>
                                @else
                                    <a href="#" class="dropdown-toggle nav-link" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-circle-user"></i>
                                        {{ Str::limit(auth('customer')->user()->nama, 10) }}
                                    </a>
                                @endif

                                <ul class="dropdown-menu dropdown-menu-end drop-pink">
                                    <li><a href="{{ route('profil') }}" class="dropdown-item nav-link-drop">
                                            <i class="fa-solid fa-user-pen"></i> Profil Saya</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="dropdown-item nav-link-drop"><i
                                                    class="fa-solid fa-right-from-bracket"></i> Log out</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                @endif

            </div>
    </nav>
    @yield('content')
    @vite('resources/js/script.js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>
