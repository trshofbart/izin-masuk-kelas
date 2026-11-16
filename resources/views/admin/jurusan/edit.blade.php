@extends('layouts.admin')
@section('title', 'Edit Jurusan')
@section('content')
<h2 class="font-bold text-2xl mb-4">Edit Jurusan — {{ $jurusan->kode }}</h2>
<div class="bg-white rounded-xl shadow p-5 max-w-xl">
    <form method="POST" action="{{ route('admin.jurusan.update', $jurusan) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-medium mb-1">Kode</label><input type="text" name="kode" value="{{ old('kode', $jurusan->kode) }}" required class="w-full border rounded-lg px-3 py-2 uppercase"></div>
        <div><label class="block text-sm font-medium mb-1">Nama Jurusan</label><input type="text" name="nama" value="{{ old('nama', $jurusan->nama) }}" required class="w-full border rounded-lg px-3 py-2"></div>
        <div class="flex gap-2">
            <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Simpan</button>
            <a href="{{ route('admin.jurusan.index') }}" class="bg-slate-200 px-5 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>
@endsection
