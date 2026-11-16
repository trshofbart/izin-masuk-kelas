<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
class IzinMasuk extends Model {
    protected $table='izin_masuk';
    protected $fillable=['kode','tanggal','nama_siswa','no_absen','kelas_id','jurusan_id','keterangan','status','diizinkan_pada','qr_masuk_id'];
    protected $casts=['tanggal'=>'date','diizinkan_pada'=>'datetime'];
    public static function generateKode(): string {
        do { $kode='MSK-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)); } while (static::where('kode',$kode)->exists());
        return $kode;
    }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function jurusan(): BelongsTo { return $this->belongsTo(Jurusan::class); }
    public function qrMasuk(): BelongsTo { return $this->belongsTo(QrMasuk::class, 'qr_masuk_id'); }
    public function isDiizinkan(): bool { return $this->status==='diizinkan'; }
    public function statusLabel(): string { return $this->isDiizinkan() ? 'Diizinkan' : 'Menunggu Scan QR'; }
    /** Tandai diizinkan lewat QR statis yang discan. Idempotent. */
    public function tandaiDiizinkan(QrMasuk $qr): void {
        if ($this->isDiizinkan()) return;
        $this->update(['status'=>'diizinkan','diizinkan_pada'=>now(),'qr_masuk_id'=>$qr->id]);
    }
}
