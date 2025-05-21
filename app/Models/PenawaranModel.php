<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenawaranModel extends Model
{
    protected $table = 'penawaran';
    protected $primaryKey = 'id_penawaran';

    protected $fillable = [
        'id_lelang',
        'id_pembeli',
        'penawaran_harga',
        'uang_muka',
        'waktu',
        'status_tawar',
        'bayar_sisa',
        'waktu_bs',
        'status_bs',
    ];

    public $timestamps = false; // karena kamu pakai timestamp custom (waktu, waktu_bs), bukan created_at & updated_at

    public function lelang()
    {
        return $this->belongsTo(LelangModel::class, 'id_lelang', 'id_lelang');
    }

    public function pembeli()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_pembeli', 'id');
    }

    public function pertemuan()
    {
        return $this->hasOne(PertemuanModel::class, 'id_penawaran', 'id_penawaran');
    }
}
