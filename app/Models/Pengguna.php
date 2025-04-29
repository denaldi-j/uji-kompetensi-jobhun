<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    protected $fillable = [
        'nama',
        'jenis_pengguna', // 'mahasiswa' or 'dosen'
        'alamat',
        'no_telp',
    ];
}
