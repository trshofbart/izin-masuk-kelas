<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8">
    <meta name="description" content="Aplikasi Izin Masuk Kelas SMKN 5 Surakarta — form izin masuk, scan QR lobi, dan rekap harian.">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <title>@yield('title', 'Izin Masuk Kelas') — SMKN 5 Surakarta</title>
    @php
        $viteSiap = false;
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            $berkasCss = $manifest['resources/css/app.css']['file'] ?? null;
            $viteSiap = $berkasCss && file_exists(public_path('build/'.$berkasCss));
        }
    @endphp
    @if ($viteSiap || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => { navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {}); });
        }
    </script>
</head>
<body class="bg-blue-50 min-h-screen text-slate-800">
    <header class="bg-blue-700 text-white shadow">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center gap-3">
            <img src="{{ asset('icons/icon-192.png') }}" alt="Logo SMKN 5" class="w-11 h-11 rounded-full bg-white p-1">
            <div>
                <h1 class="font-bold text-lg leading-tight">Izin Masuk Kelas</h1>
                <p class="text-blue-100 text-sm leading-tight">SMKN 5 Surakarta</p>
            </div>
            <a href="{{ route('izin.create') }}" class="ml-auto text-sm bg-blue-800 hover:bg-blue-900 px-3 py-2 rounded-lg">Form Baru</a>
        </div>
    </header>
    <main class="max-w-2xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                <p class="font-semibold mb-1">Periksa kembali isian berikut:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    <footer class="max-w-2xl mx-auto px-4 pb-8 text-center text-xs text-slate-500">
        SMKN 5 Surakarta &middot; Izin masuk kelas berlaku setelah scan QR di lobi berhasil. &middot; <a class="underline" href="{{ route('admin.login') }}">Login Admin</a>
    </footer>
</body>
</html>
