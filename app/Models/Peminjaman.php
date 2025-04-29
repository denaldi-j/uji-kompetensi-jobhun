<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $fillable = [
        'buku_id',
        'pengguna_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }
}
