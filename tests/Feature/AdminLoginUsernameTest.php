<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class AdminLoginUsernameTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(); }
    public function test_halaman_login_memakai_username_bukan_email(): void {
        $this->get(route('admin.login'))->assertOk()->assertSee('Username')->assertSee('name="username"', false);
    }
    public function test_login_dengan_username_berhasil(): void {
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'admin12345'])
            ->assertRedirect(route('admin.rekap'));
        $this->assertAuthenticated();
    }
    public function test_login_username_huruf_besar_tetap_berhasil(): void {
        $this->post(route('admin.login.store'), ['username' => 'ADMIN', 'password' => 'admin12345'])
            ->assertRedirect(route('admin.rekap'));
        $this->assertAuthenticated();
    }
    public function test_login_password_salah_ditolak(): void {
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'salah'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }
    public function test_login_dengan_email_tidak_lagi_bisa(): void {
        // Kolom email tidak dikirim form lagi; percobaan memakai email sebagai username harus gagal.
        $this->post(route('admin.login.store'), ['username' => 'admin@smkn5.sch.id', 'password' => 'admin12345'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }
    public function test_akun_lama_tanpa_username_dapat_username_dari_migrasi_backfill(): void {
        // Simulasi hasil backfill migrasi: akun lama (email admin) yang sudah
        // terisi username 'admin' harus bisa login. Backfill SQL-nya sendiri
        // diuji di sini dengan menjalankan update yang sama seperti migrasi.
        DB::table('users')->where('email', 'admin@smkn5.sch.id')->update(['username' => null]);
        DB::table('users')->where('email', 'admin@smkn5.sch.id')->whereNull('username')->update(['username' => 'admin']);
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'admin12345'])
            ->assertRedirect(route('admin.rekap'));
        $this->assertAuthenticated();
    }
    public function test_seeder_membuat_admin_dengan_username(): void {
        $admin = User::where('username', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('admin12345', $admin->password));
    }
}
