<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IzinMasuk;
use App\Models\Kelas;
use Illuminate\Http\Request;
class AdminController extends Controller {
    public function rekap(Request $request) {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $q = trim((string) $request->input('q',''));
        $status = $request->input('status','');
        $kelasId = $request->input('kelas_id','');
        $query = IzinMasuk::with(['kelas','jurusan','qrMasuk'])->whereDate('tanggal',$tanggal)
            ->when($q, fn($qq)=>$qq->where(fn($w)=>$w->where('nama_siswa','like',"%{$q}%")->orWhere('kode','like',"%{$q}%")->orWhere('no_absen','like',"%{$q}%")))
            ->when(in_array($status,['menunggu','diizinkan'],true), fn($qq)=>$qq->where('status',$status))
            ->when($kelasId, fn($qq)=>$qq->where('kelas_id',$kelasId))
            ->orderBy('nama_siswa');
        $list = $query->paginate(50)->withQueryString();
        $base = IzinMasuk::whereDate('tanggal',$tanggal);
        $stats = [
            'total'=>(clone $base)->count(),
            'diizinkan'=>(clone $base)->where('status','diizinkan')->count(),
            'menunggu'=>(clone $base)->where('status','menunggu')->count(),
        ];
        $kelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama')->get();
        return view('admin.rekap', compact('list','stats','tanggal','q','status','kelasId','kelas'));
    }
    public function exportCsv(Request $request) {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $rows = IzinMasuk::with(['kelas','jurusan'])->whereDate('tanggal',$tanggal)->orderBy('nama_siswa')->get();
        $filename = 'rekap-izin-masuk-'.$tanggal.'.csv';
        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output','w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Kode','Tanggal','Nama','No Absen','Kelas','Jurusan','Keterangan','Status','Diizinkan Pada'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [$r->kode, $r->tanggal->format('Y-m-d'), $r->nama_siswa, $r->no_absen, $r->kelas?->nama, $r->jurusan?->nama, $r->keterangan, $r->statusLabel(), $r->diizinkan_pada?->format('Y-m-d H:i:s')], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv; charset=UTF-8']);
    }
    /** Admin menandai manual (mis. siswa tidak membawa HP) dari rekap. */
    public function tandaiDiizinkan(IzinMasuk $izin) {
        if (!$izin->isDiizinkan()) {
            $izin->update(['status'=>'diizinkan','diizinkan_pada'=>now()]);
        }
        return back()->with('success','Izin '.$izin->kode.' ditandai Diizinkan.');
    }
}
