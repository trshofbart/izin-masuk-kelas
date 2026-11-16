<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><title>Cetak QR — {{ $qr->nama }}</title>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style>@media print { .no-print { display: none; } }</style></head>
<body class="bg-white min-h-screen flex flex-col items-center justify-center px-4 py-10 text-center">
    <img src="{{ asset('icons/icon-192.png') }}" class="w-20 h-20 mb-3" alt="">
    <h1 class="text-3xl font-extrabold">IZIN MASUK KELAS</h1>
    <p class="text-lg text-slate-600">SMKN 5 Surakarta — {{ $qr->nama }}</p>
    <div class="my-6">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(340)->generate($qr->url()) !!}</div>
    <ol class="text-left text-base space-y-1 max-w-md">
        <li>1. Isi form izin masuk di HP Anda.</li>
        <li>2. Scan QR ini dari halaman status izin Anda.</li>
        <li>3. Status berubah menjadi <strong>DIIZINKAN</strong> — silakan masuk kelas.</li>
    </ol>
    <p class="text-xs text-slate-400 mt-6 break-all">{{ $qr->url() }}</p>
    <button onclick="window.print()" class="no-print mt-6 bg-blue-700 text-white px-6 py-3 rounded-lg">🖨️ Cetak</button>
</body></html>
