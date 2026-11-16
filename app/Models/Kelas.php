<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Kelas extends Model {
    protected $table='kelas';
    protected $fillable=['jurusan_id','tingkat','nama'];
    public function jurusan(): BelongsTo { return $this->belongsTo(Jurusan::class); }
    public function izinMasuk(): HasMany { return $this->hasMany(IzinMasuk::class); }
}
