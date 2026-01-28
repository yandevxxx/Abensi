<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Group Rektor & Wakil Rektor & Dekan (Full Academic Management)
    Route::middleware(['role:rektor,wakil-rektor,dekan'])->group(function () {
        Route::get('/fakultas', [AcademicController::class, 'indexFakultas']);
        Route::post('/fakultas', [AcademicController::class, 'storeFakultas']);
        Route::get('/prodi', [AcademicController::class, 'indexProdi']);
        Route::post('/prodi', [AcademicController::class, 'storeProdi']);
        Route::get('/mata-kuliah', [AcademicController::class, 'indexMataKuliah']);
        Route::post('/mata-kuliah', [AcademicController::class, 'storeMataKuliah']);
    });

    // Group Dosen (Attendance Management)
    Route::middleware(['role:dosen'])->group(function () {
        Route::post('/absensi/open', [AttendanceController::class, 'openSession']);
        Route::get('/absensi/recap/{kelas_id}', [AttendanceController::class, 'recap']);
    });

    // Group Mahasiswa (KRS & Attendance Submit)
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::post('/krs/enroll', [EnrollmentController::class, 'store']);
        Route::post('/absensi/submit', [AttendanceController::class, 'submitAttendance']);
    });
});
