@extends('layouts.app')

@section('title', 'Pengambilan KRS')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengambilan KRS</h1>
    <div class="text-gray-600">SKS Diambil: <strong>{{ $mahasiswa->krs->sum(fn($k) => $k->kelas->mata_kuliah->sks) }} / 24</strong></div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Kuliah Tersedia (Prodi: {{ $mahasiswa->prodi->nama }})</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Smstr</th>
                        <th>Dosen</th>
                        <th>Jadwal</th>
                        <th>Kuota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($availableClasses as $kelas)
                        @php
                            $isTaken = in_array($kelas->id, $takenClassIds);
                            $isFull = $kelas->krs->count() >= $kelas->kuota;
                        @endphp
                        <tr>
                            <td>{{ $kelas->mata_kuliah->kode }}</td>
                            <td>{{ $kelas->mata_kuliah->nama }} ({{ $kelas->nama }})</td>
                            <td>{{ $kelas->mata_kuliah->sks }}</td>
                            <td>{{ $kelas->mata_kuliah->semester }}</td>
                            <td>{{ $kelas->dosen->user->name ?? '-' }}</td>
                            <td>
                                @foreach($kelas->jadwals as $j)
                                    <small>{{ $j->hari }}, {{ $j->jam_mulai }}-{{ $j->jam_selesai }} ({{ $j->ruangan }})</small><br>
                                @endforeach
                            </td>
                            <td>{{ $kelas->krs->count() }}/{{ $kelas->kuota }}</td>
                            <td>
                                @if($isTaken)
                                    <button class="btn btn-success btn-sm btn-block" disabled>Diambil</button>
                                @elseif($isFull)
                                    <button class="btn btn-secondary btn-sm btn-block" disabled>Penuh</button>
                                @else
                                    <form action="{{ route('krs.enroll') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Ambil</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
