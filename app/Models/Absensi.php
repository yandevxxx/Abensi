<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = ['kelas_id', 'pertemuan', 'tanggal', 'jam_mulai', 'jam_selesai'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function rincian()
    {
        return $this->hasMany(AbsensiDetail::class);
    }
}
