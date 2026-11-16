<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('izin_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 32)->unique();
            $table->date('tanggal');
            $table->string('nama_siswa');
            $table->string('no_absen', 20);
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('jurusan_id')->constrained('jurusan');
            $table->text('keterangan');
            $table->string('status', 20)->default('menunggu'); // menunggu|diizinkan
            $table->timestamp('diizinkan_pada')->nullable();
            $table->foreignId('qr_masuk_id')->nullable()->constrained('qr_masuk')->nullOnDelete();
            $table->timestamps();
            $table->index(['tanggal','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('izin_masuk'); }
};
