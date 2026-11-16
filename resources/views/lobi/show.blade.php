@extends('layouts.app')
@section('title', 'QR Lobi — Izin Masuk Kelas')
@section('content')
<div class="bg-white rounded-2xl shadow p-5">
    <h2 class="font-bold text-xl mb-1">📍 {{ $qr->nama }}</h2>
    <p class="text-sm text-slate-500 mb-5">Anda men-scan QR Izin Masuk Kelas. Masukkan kode izin dari halaman status Anda untuk mengubah status menjadi <strong>Diizinkan</strong>.</p>
    <form method="POST" action="{{ route('lobi.konfirmasi', $qr->kode) }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Kode Izin</label>
            <input type="text" name="kode_izin" value="{{ old('kode_izin') }}" required class="w-full border rounded-lg px-3 py-3 font-mono uppercase" placeholder="Contoh: MSK-20261116-ABCD">
        </div>
        <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg">Konfirmasi — Saya di Lobi</button>
    </form>
    <p class="text-xs text-slate-400 mt-3">Belum mengisi form? <a class="underline" href="{{ route('izin.create') }}">Isi form izin masuk dulu</a>.</p>
</div>
@if ($hariIni->count())
<div class="bg-white rounded-2xl shadow p-5 mt-4">
    <h3 class="font-bold mb-3">Sudah Diizinkan di QR Ini — Hari Ini ({{ $hariIni->count() }})</h3>
    <ul class="divide-y text-sm">
        @foreach ($hariIni as $r)
            <li class="py-2 flex justify-between gap-2"><span>{{ $r->nama_siswa }} <span class="text-slate-400">({{ $r->kelas->nama }})</span></span><span class="text-green-700 font-medium">{{ $r->diizinkan_pada->format('H:i') }}</span></li>
        @endforeach
    </ul>
</div>
@endif
@endsection
