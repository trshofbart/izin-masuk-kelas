<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class JurusanController extends Controller {
    public function index() {
        $list = Jurusan::withCount('kelas')->orderBy('kode')->get();
        return view('admin.jurusan.index', compact('list'));
    }
    public function store(Request $request) {
        $data = $this->validasi($request);
        Jurusan::create($data);
        return back()->with('success','Jurusan '.$data['kode'].' ditambahkan.');
    }
    public function edit(Jurusan $jurusan) {
        return view('admin.jurusan.edit', compact('jurusan'));
    }
    public function update(Request $request, Jurusan $jurusan) {
        $data = $this->validasi($request, $jurusan);
        $jurusan->update($data);
        return redirect()->route('admin.jurusan.index')->with('success','Jurusan '.$jurusan->kode.' diperbarui.');
    }
    public function destroy(Jurusan $jurusan) {
        if ($jurusan->kelas()->exists()) {
            return back()->withErrors(['jurusan'=>'Jurusan '.$jurusan->kode.' tidak bisa dihapus karena masih memiliki kelas. Hapus atau pindahkan kelasnya dulu.']);
        }
        if ($jurusan->izinMasuk()->exists()) {
            return back()->withErrors(['jurusan'=>'Jurusan '.$jurusan->kode.' tidak bisa dihapus karena sudah ada data izin masuk yang memakainya.']);
        }
        $kode = $jurusan->kode;
        $jurusan->delete();
        return back()->with('success','Jurusan '.$kode.' dihapus.');
    }
    private function validasi(Request $request, ?Jurusan $jurusan = null): array {
        $request->merge(['kode'=>Str::upper(trim((string) $request->input('kode','')))]);
        return $request->validate([
            'kode'=>['required','string','max:20', Rule::unique('jurusan','kode')->ignore($jurusan?->id)],
            'nama'=>['required','string','max:100'],
        ]);
    }
}
