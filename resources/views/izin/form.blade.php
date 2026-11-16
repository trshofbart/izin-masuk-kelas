@extends('layouts.app')
@section('title', 'Form Izin Masuk Kelas')
@section('content')
<div class="bg-white rounded-2xl shadow p-5">
    <h2 class="font-bold text-xl mb-1">Form Izin Masuk Kelas</h2>
    <p class="text-sm text-slate-500 mb-5">Isi form ini, lalu scan QR Izin Masuk Kelas di lobi. Setelah scan berhasil, status di halaman Anda otomatis menjadi <strong>Diizinkan</strong>.</p>
    <form method="POST" action="{{ route('izin.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="w-full border rounded-lg px-3 py-2 bg-slate-50" readonly>
            <p class="text-xs text-slate-400 mt-1">Otomatis terisi tanggal hari ini.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama Siswa <span class="text-red-500">*</span></label>
            <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}" required class="w-full border rounded-lg px-3 py-2" placeholder="Nama lengkap">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">No. Absen <span class="text-red-500">*</span></label>
            <input type="text" name="no_absen" value="{{ old('no_absen') }}" required class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: 12">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kelas <span class="text-red-500">*</span></label>
            <select name="kelas_id" id="kelas_id" required class="w-full border rounded-lg px-3 py-2">
                <option value="">— Pilih kelas —</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" data-jurusan="{{ $k->jurusan->kode }} — {{ $k->jurusan->nama }}" @selected(old('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jurusan</label>
            <input type="text" id="jurusan_label" readonly class="w-full border rounded-lg px-3 py-2 bg-slate-50" placeholder="Otomatis mengikuti kelas">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Keterangan <span class="text-red-500">*</span></label>
            <textarea name="keterangan" required rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: Terlambat karena ban motor bocor">{{ old('keterangan') }}</textarea>
        </div>
        <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg">Kirim &amp; Lanjut Scan QR</button>
    </form>
</div>
<script>
    const sel = document.getElementById('kelas_id');
    const jur = document.getElementById('jurusan_label');
    function syncJur(){ const o = sel.options[sel.selectedIndex]; jur.value = o ? (o.dataset.jurusan || '') : ''; }
    sel.addEventListener('change', syncJur); syncJur();
</script>
@endsection
