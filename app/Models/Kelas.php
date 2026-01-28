<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['mata_kuliah_id', 'dosen_id', 'nama', 'kuota'];

    public function mata_kuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function krs()
    {
        return $this->hasMany(KRS::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
