<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class UbahPasswordTest extends TestCase {
    use RefreshDatabase;
    private User $admin;
    protected function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('username', 'admin')->first();
    }
    public function test_tamu_tidak_bisa_buka_halaman_ubah_password(): void {
        $this->get(route('admin.password.edit'))->assertRedirect(route('admin.login'));
    }
    public function test_ubah_password_berhasil_dan_login_lama_gagal(): void {
        $this->actingAs($this->admin)->post(route('admin.password.update'), [
            'password_lama' => 'admin12345',
            'password_baru' => 'rahasia-baru-99',
            'password_baru_confirmation' => 'rahasia-baru-99',
        ])->assertSessionHas('success');
        $this->assertTrue(Hash::check('rahasia-baru-99', $this->admin->refresh()->password));
        Auth::logout();
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'admin12345'])->assertSessionHasErrors('username');
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'rahasia-baru-99'])->assertRedirect(route('admin.rekap'));
    }
    public function test_password_lama_salah_ditolak(): void {
        $this->actingAs($this->admin)->post(route('admin.password.update'), [
            'password_lama' => 'bukan-passwordnya',
            'password_baru' => 'rahasia-baru-99',
            'password_baru_confirmation' => 'rahasia-baru-99',
        ])->assertSessionHasErrors('password_lama');
        $this->assertTrue(Hash::check('admin12345', $this->admin->refresh()->password));
    }
    public function test_konfirmasi_beda_ditolak(): void {
        $this->actingAs($this->admin)->post(route('admin.password.update'), [
            'password_lama' => 'admin12345',
            'password_baru' => 'rahasia-baru-99',
            'password_baru_confirmation' => 'rahasia-lain-99',
        ])->assertSessionHasErrors('password_baru');
    }
    public function test_password_terlalu_pendek_ditolak(): void {
        $this->actingAs($this->admin)->post(route('admin.password.update'), [
            'password_lama' => 'admin12345',
            'password_baru' => 'abc123',
            'password_baru_confirmation' => 'abc123',
        ])->assertSessionHasErrors('password_baru');
    }
    public function test_password_baru_sama_dengan_lama_ditolak(): void {
        $this->actingAs($this->admin)->post(route('admin.password.update'), [
            'password_lama' => 'admin12345',
            'password_baru' => 'admin12345',
            'password_baru_confirmation' => 'admin12345',
        ])->assertSessionHasErrors('password_baru');
    }
}
