<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'nama_barang',
        'harga_awal',
        'lokasi',
        'deskripsi',
        'kondisi',
        'id_kategori',
        'status',
        'foto',
        'id_penjual',
    ];
    public $timestamps = true;

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'id_kategori', 'id_kategori');
    }
    public function lelang()
    {
        return $this->hasOne(LelangModel::class, 'id_barang', 'id_barang');
    }
    public function penjual()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_penjual', 'id');
    }
}