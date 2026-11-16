<?php
namespace Tests\Feature;
use App\Models\IzinMasuk;
use App\Models\Kelas;
use App\Models\QrMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class IzinMasukTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(); }
    private function kelas(): Kelas { return Kelas::first(); }
    private function buatIzin(): IzinMasuk {
        $k = $this->kelas();
        return IzinMasuk::create(['kode'=>IzinMasuk::generateKode(),'tanggal'=>today(),'nama_siswa'=>'Budi Tes','no_absen'=>'7','kelas_id'=>$k->id,'jurusan_id'=>$k->jurusan_id,'keterangan'=>'Terlambat','status'=>'menunggu']);
    }
    public function test_form_tampil_dengan_tanggal_hari_ini(): void {
        $this->get('/')->assertOk()->assertSee(now()->toDateString())->assertSee('Form Izin Masuk Kelas');
    }
    public function test_submit_form_membuat_izin_menunggu(): void {
        $k = $this->kelas();
        $resp = $this->post('/izin', ['nama_siswa'=>'Ani Tes','no_absen'=>'3','kelas_id'=>$k->id,'keterangan'=>'Ban bocor']);
        $izin = IzinMasuk::first();
        $this->assertNotNull($izin);
        $this->assertSame('menunggu', $izin->status);
        $this->assertSame($k->jurusan_id, $izin->jurusan_id);
        $this->assertTrue($izin->tanggal->isToday());
        $resp->assertRedirect(route('izin.show', $izin->kode));
    }
    public function test_submit_tanpa_keterangan_ditolak(): void {
        $this->post('/izin', ['nama_siswa'=>'Ani','no_absen'=>'3','kelas_id'=>$this->kelas()->id,'keterangan'=>''])->assertSessionHasErrors('keterangan');
    }
    public function test_scan_qr_lobi_mengubah_status_jadi_diizinkan(): void {
        $izin = $this->buatIzin();
        $qr = QrMasuk::first();
        $this->post(route('izin.scan', $izin->kode), ['qr_kode'=>$qr->kode])->assertRedirect(route('izin.show', $izin->kode));
        $izin->refresh();
        $this->assertSame('diizinkan', $izin->status);
        $this->assertNotNull($izin->diizinkan_pada);
        $this->assertSame($qr->id, $izin->qr_masuk_id);
        $this->get(route('izin.show', $izin->kode))->assertOk()->assertSee('DIIZINKAN');
    }
    public function test_scan_dengan_url_qr_penuh_juga_bisa(): void {
        $izin = $this->buatIzin();
        $qr = QrMasuk::first();
        $this->post(route('izin.scan', $izin->kode), ['qr_kode'=>$qr->url()]);
        $this->assertSame('diizinkan', $izin->refresh()->status);
    }
    public function test_scan_qr_tidak_dikenal_ditolak(): void {
        $izin = $this->buatIzin();
        $this->post(route('izin.scan', $izin->kode), ['qr_kode'=>'kode-ngawur'])->assertSessionHasErrors('qr');
        $this->assertSame('menunggu', $izin->refresh()->status);
    }
    public function test_scan_qr_nonaktif_ditolak(): void {
        $izin = $this->buatIzin();
        $qr = QrMasuk::first(); $qr->update(['aktif'=>false]);
        $this->post(route('izin.scan', $izin->kode), ['qr_kode'=>$qr->kode])->assertSessionHasErrors('qr');
        $this->assertSame('menunggu', $izin->refresh()->status);
    }
    public function test_halaman_lobi_konfirmasi_dengan_kode_izin(): void {
        $izin = $this->buatIzin();
        $qr = QrMasuk::first();
        $this->get(route('lobi.show', $qr->kode))->assertOk()->assertSee($qr->nama);
        $this->post(route('lobi.konfirmasi', $qr->kode), ['kode_izin'=>$izin->kode])->assertRedirect(route('izin.show', $izin->kode));
        $this->assertSame('diizinkan', $izin->refresh()->status);
    }
    public function test_status_json_untuk_auto_refresh(): void {
        $izin = $this->buatIzin();
        $this->getJson(route('izin.status', $izin->kode))->assertOk()->assertJsonPath('status','menunggu');
    }
    public function test_admin_harus_login_untuk_rekap(): void {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }
    public function test_admin_bisa_login_dan_lihat_rekap_harian(): void {
        $this->buatIzin();
        $admin = User::where('email','admin@smkn5.sch.id')->first();
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Rekap Harian')->assertSee('Budi Tes');
    }
    public function test_admin_generate_qr_statis_dan_cetak(): void {
        $admin = User::where('email','admin@smkn5.sch.id')->first();
        $this->actingAs($admin)->post(route('admin.qr.store'), ['nama'=>'Lobi Belakang'])->assertRedirect();
        $qr = QrMasuk::where('nama','Lobi Belakang')->first();
        $this->assertNotNull($qr);
        $this->assertNotEmpty($qr->kode);
        $this->actingAs($admin)->get(route('admin.qr.cetak', $qr))->assertOk()->assertSee('IZIN MASUK KELAS');
    }
    public function test_admin_tandai_diizinkan_manual(): void {
        $izin = $this->buatIzin();
        $admin = User::where('email','admin@smkn5.sch.id')->first();
        $this->actingAs($admin)->post(route('admin.izin.diizinkan', $izin));
        $this->assertSame('diizinkan', $izin->refresh()->status);
    }
    public function test_export_csv_rekap(): void {
        $this->buatIzin();
        $admin = User::where('email','admin@smkn5.sch.id')->first();
        $this->actingAs($admin)->get(route('admin.rekap.export', ['tanggal'=>today()->toDateString()]))->assertOk();
    }
}
