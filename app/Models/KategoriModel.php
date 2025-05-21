<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori'; // karena bukan auto-increment default

    public $incrementing = false; // penting karena primary key-nya bukan integer auto-increment
    protected $keyType = 'string'; // karena id_kategori berupa string, misalnya "KTG001"

    protected $fillable = [
        'id_kategori',
        'nama_kategori',
    ];

    public $timestamps = true; // pakai default timestamps Laravel (created_at & updated_at)
    public function barang()
    {
        return $this->hasMany(BarangModel::class, 'id_kategori', 'id_kategori');
    }
}
