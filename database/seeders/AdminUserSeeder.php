<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminUserSeeder extends Seeder {
    public function run(): void {
        // Akun admin default — login memakai USERNAME saja. GANTI PASSWORD setelah instalasi pertama!
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Admin SMKN 5 Surakarta', 'email' => 'admin@smkn5.sch.id', 'password' => Hash::make('admin12345')]
        );
        // Instalasi lama yang akunnya dibuat sebelum ada username: lengkapi usernamenya.
        if ($admin->wasRecentlyCreated === false && empty($admin->username)) {
            $admin->update(['username' => 'admin']);
        }
        $lama = User::where('email', 'admin@smkn5.sch.id')->whereNull('username')->first();
        if ($lama && $lama->id !== $admin->id) {
            $lama->update(['username' => 'admin']);
        }
    }
}
