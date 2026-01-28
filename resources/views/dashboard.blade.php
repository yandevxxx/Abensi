@extends('layouts.app')

@section('title', 'Dashboard - Akademik')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard {{ ucfirst($role) }}</h1>
</div>

@if(isset($stats))
    <!-- Content Row -->
    <div class="row">
        <!-- Total Mahasiswa Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Mahasiswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_mahasiswa'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Dosen Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Dosen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_dosen'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Prodi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Prodi
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['total_prodi'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($role === 'dosen' && isset($kelas))
    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Mengajar Anda</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Kelas</th>
                                    <th>Jadwal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas as $k)
                                    <tr>
                                        <td>{{ $k->mata_kuliah->nama }}</td>
                                        <td>{{ $k->nama }}</td>
                                        <td>
                                            @foreach($k->jadwals as $j)
                                                {{ $j->hari }}, {{ $j->jam_mulai }} - {{ $j->jam_selesai }} ({{ $j->ruangan }})<br>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary btn-sm">Buka Absensi</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($role === 'mahasiswa' && isset($krs))
    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">KRS Anda Semester Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Kelas</th>
                                    <th>Jadwal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($krs as $item)
                                    <tr>
                                        <td>{{ $item->kelas->mata_kuliah->nama }}</td>
                                        <td>{{ $item->kelas->mata_kuliah->sks }}</td>
                                        <td>{{ $item->kelas->nama }}</td>
                                        <td>
                                            @foreach($item->kelas->jadwals as $j)
                                                {{ $j->hari }}, {{ $j->jam_mulai }} - {{ $j->jam_selesai }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
