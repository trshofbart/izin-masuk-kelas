<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class KelasController extends Controller {
    public function index(Request $request) {
        $q = trim((string) $request->input('q',''));
        $jurusanId = $request->input('jurusan_id','');
        $list = Kelas::with('jurusan')->withCount('izinMasuk')
            ->when($q, fn($qq)=>$qq->where('nama','like',"%{$q}%"))
            ->when($jurusanId, fn($qq)=>$qq->where('jurusan_id',$jurusanId))
            ->orderBy('jurusan_id')->orderBy('tingkat')->orderBy('nama')
            ->paginate(50)->withQueryString();
        $jurusans = Jurusan::orderBy('kode')->get();
        return view('admin.kelas.index', compact('list','jurusans','q','jurusanId'));
    }
    public function store(Request $request) {
        $data = $this->validasi($request);
        Kelas::create($data);
        return back()->with('success','Kelas '.$data['nama'].' ditambahkan.');
    }
    public function edit(Kelas $kelas) {
        $jurusans = Jurusan::orderBy('kode')->get();
        return view('admin.kelas.edit', compact('kelas','jurusans'));
    }
    public function update(Request $request, Kelas $kelas) {
        $data = $this->validasi($request, $kelas);
        $kelas->update($data);
        return redirect()->route('admin.kelas.index')->with('success','Kelas '.$kelas->nama.' diperbarui.');
    }
    public function destroy(Kelas $kelas) {
        if ($kelas->izinMasuk()->exists()) {
            return back()->withErrors(['kelas'=>'Kelas '.$kelas->nama.' tidak bisa dihapus karena sudah ada data izin masuk yang memakainya. Ganti namanya saja lewat Edit.']);
        }
        $nama = $kelas->nama;
        $kelas->delete();
        return back()->with('success','Kelas '.$nama.' dihapus.');
    }
    private function validasi(Request $request, ?Kelas $kelas = null): array {
        $data = $request->validate([
            'jurusan_id'=>['required','exists:jurusan,id'],
            'tingkat'=>['required', Rule::in(['X','XI','XII','XIII'])],
            'nama'=>['required','string','max:50'],
        ]);
        // Kombinasi jurusan+tingkat+nama harus unik.
        $duplikat = Kelas::where('jurusan_id',$data['jurusan_id'])->where('tingkat',$data['tingkat'])->where('nama',$data['nama'])
            ->when($kelas, fn($q)=>$q->where('id','!=',$kelas->id))->exists();
        if ($duplikat) {
            throw \Illuminate\Validation\ValidationException::withMessages(['nama'=>'Kelas dengan jurusan, tingkat, dan nama ini sudah ada.']);
        }
        return $data;
    }
}
