<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertemuanModel extends Model
{
    protected $table = 'pertemuan';
    protected $primaryKey = 'id_pertemuan';

    protected $fillable = [
        'id_penawaran',
        'lokasi',
        'waktu',
        'status_terima_barang',
    ];

    public $timestamps = true; // karena tabel punya kolom created_at dan updated_at bawaan Laravel
    public function penawaran()
    {
        return $this->belongsTo(PenawaranModel::class, 'id_penawaran', 'id_penawaran');
    }
}
