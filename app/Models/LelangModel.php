<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LelangModel extends Model
{
    protected $table = 'lelang';
    protected $primaryKey = 'id_lelang';

    protected $fillable = [
        'id_barang',
        'tgl_dibuka',
        'tgl_selesai',
        'harga_akhir',
        'id_pembeli',
        'id_user',
        'status_dana',
        'id_petugas',
        'status',
    ];

    public $timestamps = false; // karena tidak pakai created_at & updated_at Laravel

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'id_barang', 'id_barang');
    }

    // Relasi ke penutup lelang
    public function penutupLelang()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_user', 'id');
    }

    // Relasi ke pembeli pemenang
    public function pembeli()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_pembeli', 'id');
    }

    // Relasi ke petugas
    public function petugas()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_petugas', 'id');
    }

    // Relasi ke penjual melalui barang
    public function penjual()
    {
        return $this->hasOneThrough(
            PenggunaModel::class,
            BarangModel::class,
            'id_barang', // Foreign key di BarangModel
            'id', // Foreign key di PenggunaModel
            'id_barang', // Local key di LelangModel
            'id_penjual' // Local key di BarangModel
        );
    }

    public function penawaran()
{
    return $this->hasMany(PenawaranModel::class, 'id_lelang', 'id_lelang');
}
}
