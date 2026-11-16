<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class PasswordController extends Controller {
    public function edit() {
        return view('admin.password');
    }
    public function update(Request $request) {
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
        ]);
        $user = $request->user();
        if (!Hash::check($data['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama salah.']);
        }
        if (Hash::check($data['password_baru'], $user->password)) {
            return back()->withErrors(['password_baru' => 'Password baru harus berbeda dari password lama.']);
        }
        $user->update(['password' => Hash::make($data['password_baru'])]);
        return back()->with('success', 'Password berhasil diubah. Login berikutnya gunakan password baru.');
    }
}
