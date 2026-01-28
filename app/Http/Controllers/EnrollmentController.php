<?php

namespace App\Http\Controllers;

use App\Models\KRS;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Ambil kelas yang mata kuliahnya sesuai prodi dan semester <= semester mahasiswa
        $availableClasses = Kelas::with(['mata_kuliah', 'dosen.user', 'jadwals'])
            ->whereHas('mata_kuliah', function ($query) use ($mahasiswa) {
                $query->where('prodi_id', $mahasiswa->prodi_id)
                      ->where('semester', '<=', $mahasiswa->semester);
            })
            ->get();

        // Ambil ID kelas yang sudah diambil
        $takenClassIds = $mahasiswa->krs->pluck('kelas_id')->toArray();

        return view('krs.index', compact('availableClasses', 'takenClassIds', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $mahasiswa = Mahasiswa::with('krs.kelas.mata_kuliah')->findOrFail($request->mahasiswa_id);
        $kelas = Kelas::with('mata_kuliah', 'jadwals')->findOrFail($request->kelas_id);

        // 1. Validasi Prodi
        if ($mahasiswa->prodi_id !== $kelas->mata_kuliah->prodi_id) {
            return back()->with('error', 'Mata kuliah tidak sesuai dengan Prodi Anda.');
        }

        // 2. Validasi Semester
        if ($kelas->mata_kuliah->semester > $mahasiswa->semester) {
            return back()->with('error', 'Anda belum bisa mengambil mata kuliah untuk semester di atas Anda.');
        }

        // 3. Validasi Mata Kuliah yang sama dua kali
        $alreadyTaken = $mahasiswa->krs()->whereHas('kelas', function($q) use ($kelas) {
            $q->where('mata_kuliah_id', $kelas->mata_kuliah_id);
        })->exists();

        if ($alreadyTaken) {
            return back()->with('error', 'Anda sudah mengambil mata kuliah ini.');
        }

        // 4. Validasi Maksimal SKS (misal 24)
        $currentSks = $mahasiswa->krs->sum(function($krs) {
            return $krs->kelas->mata_kuliah->sks;
        });

        if ($currentSks + $kelas->mata_kuliah->sks > 24) {
            return back()->with('error', 'Maksimal SKS (24) terlampaui.');
        }

        // 5. Validasi Bentrok Jadwal
        foreach ($mahasiswa->krs as $existingKrs) {
            foreach ($existingKrs->kelas->jadwals as $existingJadwal) {
                foreach ($kelas->jadwals as $newJadwal) {
                    if ($existingJadwal->hari === $newJadwal->hari) {
                        if (
                            ($newJadwal->jam_mulai >= $existingJadwal->jam_mulai && $newJadwal->jam_mulai < $existingJadwal->jam_selesai) ||
                            ($newJadwal->jam_selesai > $existingJadwal->jam_mulai && $newJadwal->jam_selesai <= $existingJadwal->jam_selesai)
                        ) {
                            return back()->with('error', "Jadwal bentrok dengan kelas {$existingKrs->kelas->mata_kuliah->nama}.");
                        }
                    }
                }
            }
        }

        // 6. Validasi Kuota
        if ($kelas->krs()->count() >= $kelas->kuota) {
            return back()->with('error', 'Kuota kelas sudah penuh.');
        }

        KRS::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kelas_id' => $kelas->id,
        ]);

        return back()->with('success', 'Berhasil mengambil mata kuliah.');
    }
}
