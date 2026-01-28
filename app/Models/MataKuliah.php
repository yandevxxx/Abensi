<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $fillable = ['prodi_id', 'nama', 'kode', 'sks', 'semester'];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}
