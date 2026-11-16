<?php
namespace App\Http\Controllers;
use App\Models\IzinMasuk;
use App\Models\Kelas;
use App\Models\QrMasuk;
use Illuminate\Http\Request;
class IzinMasukController extends Controller {
    public function create() {
        $kelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama')->get();
        return view('izin.form', compact('kelas'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'tanggal'=>['nullable','date'],
            'nama_siswa'=>['required','string','max:100'],
            'no_absen'=>['required','string','max:20'],
            'kelas_id'=>['required','exists:kelas,id'],
            'keterangan'=>['required','string','max:1000'],
        ]);
        $kelas = Kelas::with('jurusan')->findOrFail($data['kelas_id']);
        $izin = IzinMasuk::create([
            'kode'=>IzinMasuk::generateKode(),
            'tanggal'=>$data['tanggal'] ?? now()->toDateString(),
            'nama_siswa'=>$data['nama_siswa'],
            'no_absen'=>$data['no_absen'],
            'kelas_id'=>$kelas->id,
            'jurusan_id'=>$kelas->jurusan_id,
            'keterangan'=>$data['keterangan'],
            'status'=>'menunggu',
        ]);
        return redirect()->route('izin.show', $izin->kode)->with('success','Form terkirim. Silakan scan QR Izin Masuk Kelas di lobi untuk mengubah status menjadi Diizinkan.');
    }
    public function show(string $kode) {
        $izin = IzinMasuk::with(['kelas','jurusan','qrMasuk'])->where('kode',$kode)->firstOrFail();
        return view('izin.show', compact('izin'));
    }
    public function status(string $kode) {
        $izin = IzinMasuk::where('kode',$kode)->firstOrFail();
        return response()->json([
            'status'=>$izin->status,
            'label'=>$izin->statusLabel(),
            'diizinkan_pada'=>$izin->diizinkan_pada?->format('d M Y H:i'),
        ]);
    }
    /** Siswa men-scan QR statis di lobi dari halaman statusnya sendiri. */
    public function scan(Request $request, string $kode) {
        $izin = IzinMasuk::where('kode',$kode)->firstOrFail();
        $request->validate(['qr_kode'=>['required','string','max:500']]);
        $qrKode = $this->ekstrakKodeQr($request->input('qr_kode'));
        $qr = QrMasuk::where('kode',$qrKode)->where('aktif',true)->first();
        if (!$qr) {
            return back()->withErrors(['qr'=>'QR tidak dikenal atau sudah dinonaktifkan. Pastikan Anda men-scan QR Izin Masuk Kelas resmi di lobi.']);
        }
        $izin->tandaiDiizinkan($qr);
        return redirect()->route('izin.show', $izin->kode)->with('success','Scan berhasil — status Anda sekarang DIIZINKAN masuk kelas.');
    }
    /** Hasil scan kamera bisa berupa URL penuh (.../lobi/{kode}) atau kode mentah. */
    public function ekstrakKodeQr(string $hasil): string {
        $hasil = trim($hasil);
        if (preg_match('~/lobi/([^/?\s]+)~', $hasil, $m)) return urldecode(rtrim($m[1], '/'));
        return $hasil;
    }
}
