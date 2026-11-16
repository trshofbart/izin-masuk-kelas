<?php
namespace Database\Seeders;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\QrMasuk;
use Illuminate\Database\Seeder;
class MasterDataSeeder extends Seeder {
    public function run(): void {
        $jurusan = [
            ['kode'=>'RPL','nama'=>'Rekayasa Perangkat Lunak'],
            ['kode'=>'TEI','nama'=>'Teknik Elektronika Industri'],
            ['kode'=>'TPM','nama'=>'Teknik Pemesinan'],
            ['kode'=>'TOI','nama'=>'Teknik Otomasi Industri'],
            ['kode'=>'TKP','nama'=>'Teknik Konstruksi dan Perumahan'],
            ['kode'=>'DPIB','nama'=>'Desain Pemodelan dan Informasi Bangunan'],
            ['kode'=>'TITL','nama'=>'Teknik Instalasi Tenaga Listrik'],
            ['kode'=>'TAV','nama'=>'Teknik Audio Video'],
        ];
        foreach ($jurusan as $j) Jurusan::firstOrCreate(['kode'=>$j['kode']], $j);
        foreach (Jurusan::all() as $j) {
            $tingkats = ['X','XI','XII'];
            if ($j->kode==='TOI') $tingkats[]='XIII';
            foreach ($tingkats as $tingkat) {
                foreach (['A','B'] as $rombel) {
                    Kelas::firstOrCreate(['jurusan_id'=>$j->id,'tingkat'=>$tingkat,'nama'=>"$tingkat {$j->kode} $rombel"]);
                }
            }
        }
        // QR statis awal untuk lobi utama — admin bisa cetak dari menu QR Lobi.
        QrMasuk::firstOrCreate(['nama'=>'Lobi Utama'], ['aktif'=>true]);
    }
}
