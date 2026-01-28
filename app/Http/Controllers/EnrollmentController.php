<?php

namespace App\Http\Controllers;

use App\Models\KRS;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
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
            return response()->json(['message' => 'Mata kuliah tidak sesuai dengan Prodi Anda.'], 422);
        }

        // 2. Validasi Semester (biasanya mahasiswa hanya bisa ambil MK semester ganjil di semester ganjil, dst)
        // Namun ketentuan user "sesuai semester" - kita asumsikan semester MK harus <= semester mahasiswa saat ini
        if ($kelas->mata_kuliah->semester > $mahasiswa->semester) {
            return response()->json(['message' => 'Anda belum bisa mengambil mata kuliah untuk semester di atas Anda.'], 422);
        }

        // 3. Validasi Mata Kuliah yang sama dua kali
        $alreadyTaken = $mahasiswa->krs()->whereHas('kelas', function($q) use ($kelas) {
            $q->where('mata_kuliah_id', $kelas->mata_kuliah_id);
        })->exists();

        if ($alreadyTaken) {
            return response()->json(['message' => 'Anda sudah mengambil mata kuliah ini.'], 422);
        }

        // 4. Validasi Maksimal SKS (misal 24)
        $currentSks = $mahasiswa->krs->sum(function($krs) {
            return $krs->kelas->mata_kuliah->sks;
        });

        if ($currentSks + $kelas->mata_kuliah->sks > 24) {
            return response()->json(['message' => 'Maksimal SKS (24) terlampaui.'], 422);
        }

        // 5. Validasi Bentrok Jadwal
        foreach ($mahasiswa->krs as $existingKrs) {
            foreach ($existingKrs->kelas->jadwals as $existingJadwal) {
                foreach ($kelas->jadwals as $newJadwal) {
                    if ($existingJadwal->hari === $newJadwal->hari) {
                        // Cek overlap waktu
                        if (
                            ($newJadwal->jam_mulai >= $existingJadwal->jam_mulai && $newJadwal->jam_mulai < $existingJadwal->jam_selesai) ||
                            ($newJadwal->jam_selesai > $existingJadwal->jam_mulai && $newJadwal->jam_selesai <= $existingJadwal->jam_selesai)
                        ) {
                            return response()->json(['message' => "Jadwal bentrok dengan kelas {$existingKrs->kelas->mata_kuliah->nama}."], 422);
                        }
                    }
                }
            }
        }

        // 6. Validasi Kuota
        if ($kelas->krs()->count() >= $kelas->kuota) {
            return response()->json(['message' => 'Kuota kelas sudah penuh.'], 422);
        }

        KRS::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kelas_id' => $kelas->id,
        ]);

        return response()->json(['message' => 'Berhasil mengambil mata kuliah.']);
    }
}
