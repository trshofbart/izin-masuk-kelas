@extends('layouts.admin')
@section('title', 'QR Statis Lobi')
@section('content')
<h2 class="font-bold text-2xl mb-4">QR Code Statis — Masuk Kelas di Lobi</h2>
<div class="bg-white rounded-xl shadow p-5 mb-5">
    <h3 class="font-bold mb-2">Generate QR Baru</h3>
    <p class="text-sm text-slate-500 mb-3">Satu QR statis cukup untuk satu titik lobi. Kode QR tidak berubah selama tidak Anda regenerate — cetak sekali, tempel di lobi.</p>
    <form method="POST" action="{{ route('admin.qr.store') }}" class="flex gap-2 flex-wrap">@csrf
        <input type="text" name="nama" required class="flex-1 min-w-[220px] border rounded-lg px-3 py-2" placeholder="Nama titik, contoh: Lobi Utama">
        <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Generate QR Code</button>
    </form>
</div>
<div class="grid md:grid-cols-2 gap-4">
@forelse ($qrs as $qr)
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-start justify-between gap-2">
            <div><h3 class="font-bold text-lg">{{ $qr->nama }}</h3><p class="text-xs text-slate-400 font-mono">{{ $qr->kode }}</p></div>
            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $qr->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">{{ $qr->aktif ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <div class="flex justify-center py-4">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate($qr->url()) !!}</div>
        <p class="text-center text-xs text-slate-400 break-all">{{ $qr->url() }}</p>
        <p class="text-center text-sm mt-2">{{ $qr->izin_masuk_count }} siswa sudah diizinkan lewat QR ini</p>
        <div class="flex flex-wrap gap-2 mt-4 justify-center">
            <a href="{{ route('admin.qr.cetak', $qr) }}" target="_blank" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">🖨️ Cetak QR</a>
            <form method="POST" action="{{ route('admin.qr.toggle', $qr) }}">@csrf<button class="bg-slate-200 px-4 py-2 rounded-lg text-sm">{{ $qr->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
            <form method="POST" action="{{ route('admin.qr.regenerate', $qr) }}" onsubmit="return confirm('Kode QR lama akan mati dan harus cetak ulang. Lanjut?')">@csrf<button class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm">Regenerate Kode</button></form>
            <form method="POST" action="{{ route('admin.qr.destroy', $qr) }}" onsubmit="return confirm('Hapus QR ini?')">@csrf @method('DELETE')<button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Hapus</button></form>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow p-8 text-center text-slate-400 md:col-span-2">Belum ada QR. Generate QR pertama Anda di atas.</div>
@endforelse
</div>
@endsection
