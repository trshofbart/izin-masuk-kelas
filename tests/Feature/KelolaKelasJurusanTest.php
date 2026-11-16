<?php
namespace Tests\Feature;
use App\Models\IzinMasuk;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class KelolaKelasJurusanTest extends TestCase {
    use RefreshDatabase;
    private User $admin;
    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email','admin@smkn5.sch.id')->first();
    }
    public function test_tamu_tidak_bisa_buka_kelola_kelas(): void {
        $this->get(route('admin.kelas.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.jurusan.index'))->assertRedirect(route('admin.login'));
    }
    public function test_admin_bisa_tambah_edit_dan_hapus_kelas(): void {
        $jurusan = Jurusan::first();
        $this->actingAs($this->admin)->post(route('admin.kelas.store'), ['jurusan_id'=>$jurusan->id,'tingkat'=>'X','nama'=>'X '.$jurusan->kode.' Z'])->assertRedirect();
        $kelas = Kelas::where('nama','X '.$jurusan->kode.' Z')->first();
        $this->assertNotNull($kelas);
        $this->actingAs($this->admin)->put(route('admin.kelas.update', $kelas), ['jurusan_id'=>$jurusan->id,'tingkat'=>'XI','nama'=>'XI '.$jurusan->kode.' Z'])->assertRedirect(route('admin.kelas.index'));
        $this->assertSame('XI', $kelas->refresh()->tingkat);
        $this->actingAs($this->admin)->delete(route('admin.kelas.destroy', $kelas))->assertRedirect();
        $this->assertDatabaseMissing('kelas', ['id'=>$kelas->id]);
    }
    public function test_kelas_duplikat_ditolak(): void {
        $k = Kelas::first();
        $this->actingAs($this->admin)->post(route('admin.kelas.store'), ['jurusan_id'=>$k->jurusan_id,'tingkat'=>$k->tingkat,'nama'=>$k->nama])->assertSessionHasErrors('nama');
    }
    public function test_kelas_yang_sudah_dipakai_izin_tidak_bisa_dihapus(): void {
        $k = Kelas::first();
        IzinMasuk::create(['kode'=>IzinMasuk::generateKode(),'tanggal'=>today(),'nama_siswa'=>'Tes','no_absen'=>'1','kelas_id'=>$k->id,'jurusan_id'=>$k->jurusan_id,'keterangan'=>'Terlambat','status'=>'menunggu']);
        $this->actingAs($this->admin)->delete(route('admin.kelas.destroy', $k))->assertSessionHasErrors('kelas');
        $this->assertDatabaseHas('kelas', ['id'=>$k->id]);
    }
    public function test_admin_bisa_tambah_edit_dan_hapus_jurusan(): void {
        $this->actingAs($this->admin)->post(route('admin.jurusan.store'), ['kode'=>'tkj','nama'=>'Teknik Komputer dan Jaringan'])->assertRedirect();
        $j = Jurusan::where('kode','TKJ')->first();
        $this->assertNotNull($j, 'kode jurusan harus tersimpan huruf besar');
        $this->actingAs($this->admin)->put(route('admin.jurusan.update', $j), ['kode'=>'TKJ','nama'=>'Teknik Komputer Jaringan'])->assertRedirect(route('admin.jurusan.index'));
        $this->assertSame('Teknik Komputer Jaringan', $j->refresh()->nama);
        $this->actingAs($this->admin)->delete(route('admin.jurusan.destroy', $j))->assertRedirect();
        $this->assertDatabaseMissing('jurusan', ['id'=>$j->id]);
    }
    public function test_jurusan_yang_punya_kelas_tidak_bisa_dihapus(): void {
        $j = Jurusan::has('kelas')->first();
        $this->actingAs($this->admin)->delete(route('admin.jurusan.destroy', $j))->assertSessionHasErrors('jurusan');
        $this->assertDatabaseHas('jurusan', ['id'=>$j->id]);
    }
    public function test_kode_jurusan_duplikat_ditolak(): void {
        $j = Jurusan::first();
        $this->actingAs($this->admin)->post(route('admin.jurusan.store'), ['kode'=>$j->kode,'nama'=>'Duplikat'])->assertSessionHasErrors('kode');
    }
    public function test_kelas_baru_langsung_tampil_di_form_siswa(): void {
        $jurusan = Jurusan::first();
        $this->actingAs($this->admin)->post(route('admin.kelas.store'), ['jurusan_id'=>$jurusan->id,'tingkat'=>'XII','nama'=>'XII '.$jurusan->kode.' KHUSUS']);
        $this->get('/')->assertOk()->assertSee('XII '.$jurusan->kode.' KHUSUS');
    }
}
