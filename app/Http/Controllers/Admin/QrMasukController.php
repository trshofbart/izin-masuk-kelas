<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\QrMasuk;
use Illuminate\Http\Request;
class QrMasukController extends Controller {
    public function index() {
        $qrs = QrMasuk::withCount('izinMasuk')->orderBy('nama')->get();
        return view('admin.qr.index', compact('qrs'));
    }
    public function store(Request $request) {
        $data = $request->validate(['nama'=>['required','string','max:100']]);
        QrMasuk::create(['nama'=>$data['nama']]);
        return back()->with('success','QR statis baru dibuat. Silakan cetak dan tempel di lobi.');
    }
    public function cetak(QrMasuk $qr) { return view('admin.qr.cetak', compact('qr')); }
    public function toggle(QrMasuk $qr) {
        $qr->update(['aktif'=>!$qr->aktif]);
        return back()->with('success','QR '.($qr->aktif?'diaktifkan':'dinonaktifkan').'.');
    }
    public function regenerate(QrMasuk $qr) {
        $qr->update(['kode'=>QrMasuk::generateKode()]);
        return back()->with('success','Kode QR diganti. QR cetak lama tidak berlaku lagi — cetak ulang.');
    }
    public function destroy(QrMasuk $qr) {
        $qr->delete();
        return back()->with('success','QR dihapus.');
    }
}
