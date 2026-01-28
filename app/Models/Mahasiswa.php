<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = ['user_id', 'prodi_id', 'nim', 'semester'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function krs()
    {
        return $this->hasMany(KRS::class);
    }

    public function absensi_details()
    {
        return $this->hasMany(AbsensiDetail::class);
    }
}
