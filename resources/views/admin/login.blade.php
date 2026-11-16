<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin — Izin Masuk Kelas SMKN 5</title>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
<div class="bg-white rounded-2xl shadow p-6 w-full max-w-sm">
    <h1 class="font-bold text-xl mb-1">Login Admin</h1>
    <p class="text-sm text-slate-500 mb-5">Izin Masuk Kelas — SMKN 5 Surakarta</p>
    @if ($errors->any())<div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">@csrf
        <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">Password</label><input type="password" name="password" required class="w-full border rounded-lg px-3 py-2"></div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg">Masuk</button>
    </form>
    <p class="text-xs text-slate-400 mt-4 text-center"><a class="underline" href="{{ route('izin.create') }}">Kembali ke form siswa</a></p>
</div></body></html>
