<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Kelas;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Dosen membuka sesi absensi
     */
    public function openSession(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'pertemuan' => 'required|integer',
        ]);

        $kelas = Kelas::with('jadwals')->findOrFail($request->kelas_id);
        $now = Carbon::now();
        $today = $now->translatedFormat('l'); // Get day name in local

        // Mapping English day to Indonesian as stored in DB
        $dayMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $hariIndo = $dayMap[$now->format('l')];

        // Validasi apakah hari ini ada jadwal untuk kelas ini
        $jadwal = $kelas->jadwals()->where('hari', $hariIndo)->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Tidak ada jadwal kuliah untuk kelas ini hari ini.'], 422);
        }

        // Validasi Jam (Hanya bisa dibuka saat jam kuliah)
        $currentTime = $now->format('H:i:s');
        if ($currentTime < $jadwal->jam_mulai || $currentTime > $jadwal->jam_selesai) {
            return response()->json(['message' => 'Absensi hanya bisa dibuka saat jam kuliah berlangsung.'], 422);
        }

        $absensi = Absensi::updateOrCreate(
            ['kelas_id' => $kelas->id, 'pertemuan' => $request->pertemuan],
            [
                'tanggal' => $now->toDateString(),
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
            ]
        );

        return response()->json(['message' => 'Sesi absensi berhasil dibuka.', 'data' => $absensi]);
    }

    /**
     * Mahasiswa melakukan absensi
     */
    public function submitAttendance(Request $request)
    {
        $request->validate([
            'absensi_id' => 'required|exists:absensis,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $absensi = Absensi::findOrFail($request->absensi_id);
        $now = Carbon::now();

        // Validasi waktu absensi mahasiswa
        if ($now->toDateString() !== $absensi->tanggal) {
            return response()->json(['message' => 'Sesi absensi ini bukan untuk hari ini.'], 422);
        }

        $currentTime = $now->format('H:i:s');
        if ($currentTime < $absensi->jam_mulai || $currentTime > $absensi->jam_selesai) {
            return response()->json(['message' => 'Waktu absensi sudah berakhir atau belum dimulai.'], 422);
        }

        $detail = AbsensiDetail::updateOrCreate(
            ['absensi_id' => $absensi->id, 'mahasiswa_id' => $request->mahasiswa_id],
            ['status' => $request->status]
        );

        return response()->json(['message' => 'Absensi berhasil dicatat.', 'data' => $detail]);
    }

    /**
     * Rekap Absen
     */
    public function recap($kelas_id)
    {
        $recap = Absensi::with('rincian.mahasiswa.user')
            ->where('kelas_id', $kelas_id)
            ->get();

        return response()->json($recap);
    }
}
