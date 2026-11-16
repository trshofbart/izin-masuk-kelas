@extends('layouts.admin')
@section('title', 'Kelola Kelas')
@section('content')
<h2 class="font-bold text-2xl mb-4">Kelola Kelas</h2>
<div class="bg-white rounded-xl shadow p-5 mb-5">
    <h3 class="font-bold mb-3">Tambah Kelas</h3>
    <form method="POST" action="{{ route('admin.kelas.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div><label class="block text-sm font-medium mb-1">Jurusan</label>
            <select name="jurusan_id" required class="border rounded-lg px-3 py-2">
                <option value="">— Pilih jurusan —</option>
                @foreach ($jurusans as $j)<option value="{{ $j->id }}" @selected(old('jurusan_id')==$j->id)>{{ $j->kode }} — {{ $j->nama }}</option>@endforeach
            </select></div>
        <div><label class="block text-sm font-medium mb-1">Tingkat</label>
            <select name="tingkat" required class="border rounded-lg px-3 py-2">
                @foreach (['X','XI','XII','XIII'] as $t)<option value="{{ $t }}" @selected(old('tingkat')===$t)>{{ $t }}</option>@endforeach
            </select></div>
        <div class="flex-1 min-w-[200px]"><label class="block text-sm font-medium mb-1">Nama Kelas</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: X RPL A"></div>
        <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Tambah Kelas</button>
    </form>
</div>
<form method="GET" action="{{ route('admin.kelas.index') }}" class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[180px]"><label class="block text-sm font-medium mb-1">Cari nama kelas</label><input type="text" name="q" value="{{ $q }}" class="w-full border rounded-lg px-3 py-2" placeholder="Cari..."></div>
    <div><label class="block text-sm font-medium mb-1">Jurusan</label>
        <select name="jurusan_id" class="border rounded-lg px-3 py-2"><option value="">Semua jurusan</option>
            @foreach ($jurusans as $j)<option value="{{ $j->id }}" @selected((string)$jurusanId===(string)$j->id)>{{ $j->kode }}</option>@endforeach
        </select></div>
    <button class="bg-slate-800 text-white px-4 py-2 rounded-lg">Filter</button>
</form>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Nama Kelas</th><th class="px-4 py-3">Tingkat</th><th class="px-4 py-3">Jurusan</th><th class="px-4 py-3">Data Izin</th><th class="px-4 py-3">Aksi</th></tr></thead>
        <tbody class="divide-y">
        @forelse ($list as $k)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $k->nama }}</td>
                <td class="px-4 py-3">{{ $k->tingkat }}</td>
                <td class="px-4 py-3">{{ $k->jurusan->kode }} — {{ $k->jurusan->nama }}</td>
                <td class="px-4 py-3">{{ $k->izin_masuk_count }} izin</td>
                <td class="px-4 py-3"><div class="flex gap-3">
                    <a href="{{ route('admin.kelas.edit', $k) }}" class="text-blue-700 underline">Edit</a>
                    <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" onsubmit="return confirm('Hapus kelas {{ $k->nama }}?')">@csrf @method('DELETE')<button class="text-red-600 underline">Hapus</button></form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Tidak ada kelas untuk filter ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $list->links() }}</div>
@endsection
