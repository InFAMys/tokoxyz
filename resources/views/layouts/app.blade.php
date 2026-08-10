<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    @php
        // Which SECTION of the site is this? Determine that from the URL
        // first, then check only that guard — not "whichever guard happens
// to still be logged in," since owner/pegawai/customer are
        // independent sessions and more than one can be active at once.
        $currentGuard = match (true) {
            request()->is('owner') || request()->is('owner/*') => 'owner',
            request()->is('pegawai') || request()->is('pegawai/*') => 'pegawai',
            default => 'customer',
        };

        $activeGuard = auth($currentGuard)->check() ? $currentGuard : null;
    @endphp

    <nav class="bg-white shadow">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            <span class="font-semibold text-gray-800">{{ config('app.name', 'Laravel') }}</span>
            @if ($activeGuard)
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500">
                        @if ($activeGuard == 'customer')
                            {{ auth($activeGuard)->user()->nama }} ({{ $activeGuard }})
                        @elseif($activeGuard == 'pegawai')
                            {{ auth($activeGuard)->user()->nama_pegawai }} ({{ $activeGuard }})
                        @elseif($activeGuard == 'owner')
                            {{ auth($activeGuard)->user()->username }} ({{ $activeGuard }})
                        @endif
                    </span>

                    @if ($activeGuard == 'customer')
                        <form method="POST" action="{{ route('logout') }}">
                            <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:underline">Edit
                                profile</a>
                        @else
                            <a href="{{ route($activeGuard . '.profile.edit') }}"
                                class="text-indigo-600 hover:underline">Edit
                                profile</a>
                            <form method="POST" action="{{ route($activeGuard . '.logout') }}">
                    @endif

                    @csrf
                    <button class="text-red-600 hover:underline">Log out</button>
                    </form>
                </div>
            @endif
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-10">
        @yield('content')
    </main>
</body>

</html>
