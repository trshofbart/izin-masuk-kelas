<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
class QrMasuk extends Model {
    protected $table='qr_masuk';
    protected $fillable=['nama','kode','aktif'];
    protected $casts=['aktif'=>'boolean'];
    protected static function booted(): void {
        static::creating(function (QrMasuk $qr) {
            if (empty($qr->kode)) $qr->kode = static::generateKode();
        });
    }
    public static function generateKode(): string {
        do { $kode = Str::lower(Str::random(24)); } while (static::where('kode',$kode)->exists());
        return $kode;
    }
    public function url(): string { return route('lobi.show', $this->kode); }
    public function izinMasuk(): HasMany { return $this->hasMany(IzinMasuk::class, 'qr_masuk_id'); }
}
