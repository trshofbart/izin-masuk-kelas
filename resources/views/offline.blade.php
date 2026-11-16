@extends('layouts.app')
@section('title', 'Offline')
@section('content')
<div class="bg-white rounded-2xl shadow p-6 text-center">
    <p class="text-4xl mb-3">📴</p>
    <h2 class="font-bold text-xl mb-2">Anda sedang offline</h2>
    <p class="text-sm text-slate-500">Form izin masuk dan scan QR membutuhkan koneksi internet. Sambungkan kembali, lalu coba lagi.</p>
    <a href="{{ route('izin.create') }}" class="inline-block mt-4 bg-blue-700 text-white px-5 py-2 rounded-lg">Coba Lagi</a>
</div>
@endsection
