<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PenggunaModel extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'pengguna';
    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'password',
        'nama',
        'telepon',
        'alamat',
        'bank',
        'no_rekening',
        'role',
        'foto',
    ];

    public $timestamps = true;

    protected $hidden = [
        'password',
    ];

    public function lelangDisetujui()
    {
        return $this->hasMany(LelangModel::class, 'id_petugas')->where('status', 'disetujui');
    }

    // Untuk pembeli: menghitung lelang yang diikuti (melalui tabel bid)
    public function lelangDiikuti()
    {
        return $this->hasMany(PenawaranModel::class, 'id_pembeli')->distinct('id_lelang');
    }

    // Untuk pembeli: menghitung lelang yang dimenangkan
    public function lelangDimenangkan()
    {
        return $this->hasMany(LelangModel::class, 'id_pembeli');
    }

     // Untuk penjual: relasi ke barang yang dijual
    public function barang()
    {
        return $this->hasMany(BarangModel::class, 'id_penjual');
    }

    // Untuk penjual: menghitung lelang aktif (melalui barang -> lelang)
    public function lelangAktif()
    {
        return $this->hasManyThrough(
            LelangModel::class,
            BarangModel::class,
            'id_penjual', // Foreign key on BarangModel table
            'id_barang',   // Foreign key on LelangModel table
            'id',          // Local key on PenggunaModel table
            'id_barang'    // Local key on BarangModel table
        )->where('lelang.status', 'dibuka');
    }

    // Untuk penjual: menghitung lelang selesai (melalui barang -> lelang)
    public function lelangSelesai()
    {
        return $this->hasManyThrough(
            LelangModel::class,
            BarangModel::class,
            'id_penjual', // Foreign key on BarangModel table
            'id_barang',   // Foreign key on LelangModel table
            'id',          // Local key on PenggunaModel table
            'id_barang'    // Local key on BarangModel table
        )->where('lelang.status', 'ditutup');
    }
}
