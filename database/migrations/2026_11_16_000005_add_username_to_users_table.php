<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
        });
        // Instalasi lama: akun admin yang sudah ada (dibuat seeder versi email)
        // diberi username 'admin' supaya langsung bisa login dengan username
        // tanpa perlu seed ulang / edit database manual.
        DB::table('users')->where('email', 'admin@smkn5.sch.id')->whereNull('username')->update(['username' => 'admin']);
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
