@extends('layouts.admin')
@section('title', 'Ubah Password')
@section('content')
<h2 class="font-bold text-2xl mb-4">Ubah Password</h2>
<div class="bg-white rounded-xl shadow p-5 max-w-xl">
    <p class="text-sm text-slate-500 mb-4">Login sebagai <strong>{{ auth()->user()->username }}</strong> ({{ auth()->user()->name }}). Password baru minimal 8 karakter.</p>
    <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
        @csrf
        <div><label class="block text-sm font-medium mb-1">Password Lama</label>
            <input type="password" name="password_lama" required class="w-full border rounded-lg px-3 py-2" autocomplete="current-password"></div>
        <div><label class="block text-sm font-medium mb-1">Password Baru</label>
            <input type="password" name="password_baru" required class="w-full border rounded-lg px-3 py-2" autocomplete="new-password"></div>
        <div><label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_baru_confirmation" required class="w-full border rounded-lg px-3 py-2" autocomplete="new-password"></div>
        <button class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg font-semibold">Simpan Password Baru</button>
    </form>
</div>
@endsection
