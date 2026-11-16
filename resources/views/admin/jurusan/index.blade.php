@extends('layouts.admin')
@section('title', 'Kelola Jurusan')
@section('content')
<h2 class="font-bold text-2xl mb-4">Kelola Jurusan</h2>
<div class="bg-white rounded-xl shadow p-5 mb-5">
    <h3 class="font-bold mb-3">Tambah Jurusan</h3>
    <form method="POST" action="{{ route('admin.jurusan.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div><label class="block text-sm font-medium mb-1">Kode</label><input type="text" name="kode" value="{{ old('kode') }}" required class="border rounded-lg px-3 py-2 uppercase" placeholder="Contoh: TKJ"></div>
        <div class="flex-1 min-w-[220px]"><label class="block text-sm font-medium mb-1">Nama Jurusan</label><input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border rounded-lg px-3 py-2" placeholder="Contoh: Teknik Komputer dan Jaringan"></div>
        <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Tambah Jurusan</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-[520px]">
        <thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Kode</th><th class="px-4 py-3">Nama Jurusan</th><th class="px-4 py-3">Jumlah Kelas</th><th class="px-4 py-3">Aksi</th></tr></thead>
        <tbody class="divide-y">
        @forelse ($list as $j)
            <tr>
                <td class="px-4 py-3 font-mono font-semibold">{{ $j->kode }}</td>
                <td class="px-4 py-3">{{ $j->nama }}</td>
                <td class="px-4 py-3">{{ $j->kelas_count }} kelas</td>
                <td class="px-4 py-3"><div class="flex gap-3">
                    <a href="{{ route('admin.jurusan.edit', $j) }}" class="text-blue-700 underline">Edit</a>
                    <form method="POST" action="{{ route('admin.jurusan.destroy', $j) }}" onsubmit="return confirm('Hapus jurusan {{ $j->kode }}?')">@csrf @method('DELETE')<button class="text-red-600 underline">Hapus</button></form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada jurusan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
