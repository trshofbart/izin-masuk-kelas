<?php
namespace App\Http\Controllers;
use App\Models\IzinMasuk;
use App\Models\QrMasuk;
use Illuminate\Http\Request;
class LobiController extends Controller {
    /** Halaman tujuan QR statis yang dicetak & ditempel di lobi. */
    public function show(string $kode) {
        $qr = QrMasuk::where('kode',$kode)->where('aktif',true)->firstOrFail();
        $hariIni = IzinMasuk::with(['kelas','jurusan'])->whereDate('tanggal', today())->where('qr_masuk_id',$qr->id)->where('status','diizinkan')->latest('diizinkan_pada')->get();
        return view('lobi.show', compact('qr','hariIni'));
    }
    /** Siswa memasukkan kode izinnya di halaman lobi setelah scan QR cetak. */
    public function konfirmasi(Request $request, string $kode) {
        $qr = QrMasuk::where('kode',$kode)->where('aktif',true)->firstOrFail();
        $request->validate(['kode_izin'=>['required','string','max:32']]);
        $izin = IzinMasuk::where('kode', strtoupper(trim($request->input('kode_izin'))))->first();
        if (!$izin) {
            return back()->withErrors(['kode_izin'=>'Kode izin tidak ditemukan. Periksa kembali kode di halaman status Anda.'])->withInput();
        }
        if (!$izin->tanggal->isToday()) {
            return back()->withErrors(['kode_izin'=>'Izin ini bukan untuk hari ini, jadi tidak bisa dikonfirmasi di lobi.'])->withInput();
        }
        $izin->tandaiDiizinkan($qr);
        return redirect()->route('izin.show', $izin->kode)->with('success','Berhasil — status Anda sekarang DIIZINKAN masuk kelas.');
    }
}
