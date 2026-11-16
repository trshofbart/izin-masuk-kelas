<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e293b">
    <title>@yield('title', 'Admin') — Izin Masuk Kelas SMKN 5</title>
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
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">
    <header class="bg-slate-800 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-2 flex-wrap">
            <span class="font-bold">Admin — Izin Masuk Kelas</span>
            <nav class="ml-4 flex gap-2 text-sm">
                <a href="{{ route('admin.rekap') }}" class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600">Rekap Harian</a>
                <a href="{{ route('admin.qr.index') }}" class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600">QR Lobi</a>
                <a href="{{ route('admin.kelas.index') }}" class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600">Kelas</a>
                <a href="{{ route('admin.jurusan.index') }}" class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600">Jurusan</a>
                <a href="{{ route('izin.create') }}" class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600">Lihat Form Siswa</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">@csrf
                <button class="text-sm bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg">Keluar ({{ auth()->user()->name }})</button>
            </form>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
