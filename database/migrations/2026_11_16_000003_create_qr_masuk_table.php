<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('qr_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 64)->unique();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('qr_masuk'); }
};
