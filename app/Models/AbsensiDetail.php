<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiDetail extends Model
{
    protected $fillable = ['absensi_id', 'mahasiswa_id', 'status'];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
