<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDanaModel extends Model
{
    protected $table = 'pencairandana';
    protected $primaryKey = 'id_pencairan';

    protected $fillable = [
        'nominal',
        'id_lelang',
        'id_penjual',
        'waktu',
    ];

    public $timestamps = false; // karena pakai kolom waktu sendiri, bukan created_at/updated_at
    public function lelang()
    {
        return $this->belongsTo(LelangModel::class, 'id_lelang', 'id_lelang');
    }

    public function penjual()
    {
        return $this->belongsTo(PenggunaModel::class, 'id_penjual', 'id');
    }
}
