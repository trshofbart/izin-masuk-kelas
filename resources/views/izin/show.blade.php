@extends('layouts.app')
@section('title', 'Status Izin Masuk')
@section('content')
<div class="bg-white rounded-2xl shadow p-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-slate-400">Kode Izin</p>
            <p class="font-mono font-bold text-lg">{{ $izin->kode }}</p>
        </div>
        <span id="status-badge" class="px-3 py-1 rounded-full text-sm font-semibold {{ $izin->isDiizinkan() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $izin->statusLabel() }}</span>
    </div>
    <dl class="mt-4 space-y-2 text-sm">
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Tanggal</dt><dd class="font-medium">{{ $izin->tanggal->format('d M Y') }}</dd></div>
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $izin->nama_siswa }}</dd></div>
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">No. Absen</dt><dd class="font-medium">{{ $izin->no_absen }}</dd></div>
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Kelas</dt><dd class="font-medium">{{ $izin->kelas->nama }}</dd></div>
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Jurusan</dt><dd class="font-medium">{{ $izin->jurusan->kode }} — {{ $izin->jurusan->nama }}</dd></div>
        <div class="flex justify-between border-b pb-2"><dt class="text-slate-500">Keterangan</dt><dd class="font-medium text-right">{{ $izin->keterangan }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-500">Status / Keterangan Halaman</dt><dd id="status-text" class="font-bold {{ $izin->isDiizinkan() ? 'text-green-700' : 'text-amber-700' }}">{{ $izin->isDiizinkan() ? 'Diizinkan' : 'Belum diizinkan — menunggu scan QR lobi' }}</dd></div>
        @if ($izin->diizinkan_pada)
            <div class="flex justify-between"><dt class="text-slate-500">Diizinkan pada</dt><dd class="font-medium">{{ $izin->diizinkan_pada->format('d M Y H:i') }}@if($izin->qrMasuk) via {{ $izin->qrMasuk->nama }}@endif</dd></div>
        @endif
    </dl>

    @if ($izin->isDiizinkan())
        <div class="mt-5 bg-green-600 text-white text-center rounded-xl py-5 px-4">
            <p class="text-2xl font-extrabold tracking-wide">DIIZINKAN</p>
            <p class="text-green-100 text-sm mt-1">Silakan masuk kelas. Tunjukkan halaman ini bila diminta.</p>
        </div>
    @else
        <div class="mt-5 border-2 border-dashed border-blue-300 rounded-xl p-4">
            <h3 class="font-bold mb-1">Langkah terakhir: scan QR di lobi</h3>
            <p class="text-sm text-slate-500 mb-3">Tekan tombol di bawah, lalu arahkan kamera ke QR Izin Masuk Kelas yang tertempel di lobi. Setelah berhasil, keterangan di halaman ini otomatis berubah menjadi <strong>Diizinkan</strong>.</p>
            <button id="btn-scan" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg">📷 Scan QR Izin Masuk Kelas</button>
            <div id="reader" class="mt-3 hidden"></div>
            <p id="scan-error" class="hidden mt-2 text-sm text-red-600"></p>
            <form id="scan-form" method="POST" action="{{ route('izin.scan', $izin->kode) }}" class="mt-4">
                @csrf
                <label class="block text-sm font-medium mb-1">Atau tempel kode / URL QR secara manual</label>
                <div class="flex gap-2">
                    <input type="text" name="qr_kode" class="flex-1 border rounded-lg px-3 py-2" placeholder="Kode QR lobi">
                    <button class="bg-slate-800 text-white px-4 rounded-lg">Konfirmasi</button>
                </div>
            </form>
            <p class="text-xs text-slate-400 mt-3">Cara lain: scan QR cetak di lobi dengan aplikasi kamera HP — halaman lobi akan terbuka, lalu masukkan kode izin <span class="font-mono font-semibold">{{ $izin->kode }}</span>.</p>
        </div>
    @endif
</div>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const btn = document.getElementById('btn-scan');
    if (btn) {
        let scanner = null;
        btn.addEventListener('click', async () => {
            const reader = document.getElementById('reader');
            const err = document.getElementById('scan-error');
            reader.classList.remove('hidden'); err.classList.add('hidden');
            try {
                scanner = new Html5Qrcode('reader');
                await scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 },
                    (decoded) => {
                        scanner.stop().catch(()=>{});
                        const form = document.getElementById('scan-form');
                        form.querySelector('input[name="qr_kode"]').value = decoded;
                        form.submit();
                    }, () => {});
            } catch (e) {
                err.textContent = 'Kamera tidak bisa dibuka (' + e + '). Gunakan kolom manual di bawah, atau scan QR lobi dengan aplikasi kamera HP.';
                err.classList.remove('hidden');
            }
        });
    }
    // Auto-refresh status: bila konfirmasi dilakukan lewat halaman lobi di tab/perangkat lain, halaman ini berubah sendiri.
    @unless ($izin->isDiizinkan())
    setInterval(async () => {
        try {
            const r = await fetch('{{ route('izin.status', $izin->kode) }}', { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            if (d.status === 'diizinkan') location.reload();
        } catch (e) {}
    }, 3000);
    @endunless
</script>
@endsection
