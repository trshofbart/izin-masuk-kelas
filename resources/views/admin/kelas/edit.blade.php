@extends('layouts.admin')
@section('title', 'Edit Kelas')
@section('content')
<h2 class="font-bold text-2xl mb-4">Edit Kelas — {{ $kelas->nama }}</h2>
<div class="bg-white rounded-xl shadow p-5 max-w-xl">
    <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-medium mb-1">Jurusan</label>
            <select name="jurusan_id" required class="w-full border rounded-lg px-3 py-2">
                @foreach ($jurusans as $j)<option value="{{ $j->id }}" @selected(old('jurusan_id', $kelas->jurusan_id)==$j->id)>{{ $j->kode }} — {{ $j->nama }}</option>@endforeach
            </select></div>
        <div><label class="block text-sm font-medium mb-1">Tingkat</label>
            <select name="tingkat" required class="w-full border rounded-lg px-3 py-2">
                @foreach (['X','XI','XII','XIII'] as $t)<option value="{{ $t }}" @selected(old('tingkat', $kelas->tingkat)===$t)>{{ $t }}</option>@endforeach
            </select></div>
        <div><label class="block text-sm font-medium mb-1">Nama Kelas</label>
            <input type="text" name="nama" value="{{ old('nama', $kelas->nama) }}" required class="w-full border rounded-lg px-3 py-2"></div>
        <div class="flex gap-2">
            <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Simpan</button>
            <a href="{{ route('admin.kelas.index') }}" class="bg-slate-200 px-5 py-2 rounded-lg">Batal</a>
        </div>
    </form>
</div>
@endsection
