<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $fillable = ['fakultas_id', 'nama', 'kode'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function mata_kuliahs()
    {
        return $this->hasMany(MataKuliah::class);
    }
}
