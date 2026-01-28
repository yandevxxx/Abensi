<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->slug;

        $data = [
            'user' => $user,
            'role' => $role,
        ];

        switch ($role) {
            case 'rektor':
            case 'wakil-rektor':
                $data['stats'] = [
                    'total_mahasiswa' => \App\Models\Mahasiswa::count(),
                    'total_dosen' => \App\Models\Dosen::count(),
                    'total_prodi' => \App\Models\Prodi::count(),
                ];
                break;
            
            case 'dosen':
                $dosen = $user->dosen; // Assuming relationship is defined
                $data['kelas'] = $dosen ? $dosen->kelas()->with('mata_kuliah', 'jadwals')->get() : [];
                break;

            case 'mahasiswa':
                $mahasiswa = $user->mahasiswa;
                $data['krs'] = $mahasiswa ? $mahasiswa->krs()->with('kelas.mata_kuliah', 'kelas.jadwals')->get() : [];
                break;
        }

        return view('dashboard', $data);
    }
}
