<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
    public function loginForm() {
        if (Auth::check()) return redirect()->route('admin.rekap');
        return view('admin.login');
    }
    public function login(Request $request) {
        // Login admin hanya memakai username + password (tanpa email).
        $cred = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);
        $cred['username'] = strtolower(trim($cred['username']));
        if (!Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }
        $request->session()->regenerate();
        return redirect()->intended(route('admin.rekap'));
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
