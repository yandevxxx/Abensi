<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    /**
     * CRUD Fakultas
     */
    public function indexFakultas()
    {
        return response()->json(Fakultas::all());
    }

    public function storeFakultas(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'kode' => 'required|string|unique:fakultas,kode',
        ]);

        return response()->json(Fakultas::create($validated));
    }

    /**
     * CRUD Prodi
     */
    public function indexProdi()
    {
        return response()->json(Prodi::with('fakultas')->get());
    }

    public function storeProdi(Request $request)
    {
        $validated = $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'nama' => 'required|string',
            'kode' => 'required|string|unique:prodis,kode',
        ]);

        return response()->json(Prodi::create($validated));
    }

    /**
     * CRUD Mata Kuliah
     */
    public function indexMataKuliah()
    {
        return response()->json(MataKuliah::with('prodi')->get());
    }

    public function storeMataKuliah(Request $request)
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodis,id',
            'nama' => 'required|string',
            'kode' => 'required|string|unique:mata_kuliahs,kode',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        return response()->json(MataKuliah::create($validated));
    }
}
