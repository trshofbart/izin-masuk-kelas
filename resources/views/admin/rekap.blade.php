@extends('layouts.admin')
@section('title', 'Rekap Harian')
@section('content')
<h2 class="font-bold text-2xl mb-4">Rekap Harian — {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</h2>
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-white rounded-xl shadow p-4 text-center"><p class="text-3xl font-extrabold">{{ $stats['total'] }}</p><p class="text-sm text-slate-500">Total Izin</p></div>
    <div class="bg-white rounded-xl shadow p-4 text-center"><p class="text-3xl font-extrabold text-green-700">{{ $stats['diizinkan'] }}</p><p class="text-sm text-slate-500">Diizinkan</p></div>
    <div class="bg-white rounded-xl shadow p-4 text-center"><p class="text-3xl font-extrabold text-amber-600">{{ $stats['menunggu'] }}</p><p class="text-sm text-slate-500">Menunggu Scan</p></div>
</div>
<form method="GET" action="{{ route('admin.rekap') }}" class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div><label class="block text-sm font-medium mb-1">Tanggal</label><input type="date" name="tanggal" value="{{ $tanggal }}" class="border rounded-lg px-3 py-2"></div>
    <div class="flex-1 min-w-[180px]"><label class="block text-sm font-medium mb-1">Cari nama / kode / no. absen</label><input type="text" name="q" value="{{ $q }}" class="w-full border rounded-lg px-3 py-2" placeholder="Cari..."></div>
    <div><label class="block text-sm font-medium mb-1">Status</label><select name="status" class="border rounded-lg px-3 py-2"><option value="">Semua</option><option value="menunggu" @selected($status==='menunggu')>Menunggu Scan</option><option value="diizinkan" @selected($status==='diizinkan')>Diizinkan</option></select></div>
    <div><label class="block text-sm font-medium mb-1">Kelas</label><select name="kelas_id" class="border rounded-lg px-3 py-2"><option value="">Semua kelas</option>@foreach($kelas as $k)<option value="{{ $k->id }}" @selected((string)$kelasId===(string)$k->id)>{{ $k->nama }}</option>@endforeach</select></div>
    <button class="bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
    <a href="{{ route('admin.rekap.export', ['tanggal'=>$tanggal]) }}" class="bg-green-700 text-white px-4 py-2 rounded-lg">Export CSV</a>
</form>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-[820px]">
        <thead class="bg-slate-50 text-left"><tr>
            <th class="px-4 py-3">Kode</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">No. Absen</th><th class="px-4 py-3">Kelas</th><th class="px-4 py-3">Jurusan</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Diizinkan Pada</th><th class="px-4 py-3">Aksi</th>
        </tr></thead>
        <tbody class="divide-y">
        @forelse ($list as $r)
            <tr>
                <td class="px-4 py-3 font-mono">{{ $r->kode }}</td>
                <td class="px-4 py-3 font-medium">{{ $r->nama_siswa }}</td>
                <td class="px-4 py-3">{{ $r->no_absen }}</td>
                <td class="px-4 py-3">{{ $r->kelas?->nama }}</td>
                <td class="px-4 py-3">{{ $r->jurusan?->kode }}</td>
                <td class="px-4 py-3">{{ $r->keterangan }}</td>
                <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-semibold {{ $r->isDiizinkan() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $r->statusLabel() }}</span></td>
                <td class="px-4 py-3">{{ $r->diizinkan_pada?->format('H:i') ?? '—' }}@if($r->qrMasuk)<span class="text-slate-400"> ({{ $r->qrMasuk->nama }})</span>@endif</td>
                <td class="px-4 py-3">@unless($r->isDiizinkan())<form method="POST" action="{{ route('admin.izin.diizinkan', $r) }}">@csrf<button class="text-blue-700 underline">Tandai Diizinkan</button></form>@else<span class="text-slate-400">—</span>@endunless</td>
            </tr>
        @empty
            <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada data izin untuk filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $list->links() }}</div>
@endsection
