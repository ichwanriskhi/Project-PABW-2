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
}
