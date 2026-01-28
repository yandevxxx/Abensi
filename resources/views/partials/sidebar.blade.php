<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-university"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Akademik</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    @if(Auth::check())
        @php
            $role = Auth::user()->role->slug;
        @endphp

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Sistem
        </div>

        @if(in_array($role, ['rektor', 'wakil-rektor', 'dekan']))
            <!-- Nav Item - Sarana & Prasarana -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAcademic"
                    aria-expanded="true" aria-controls="collapseAcademic">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Akademik</span>
                </a>
                <div id="collapseAcademic" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="#">Fakultas</a>
                        <a class="collapse-item" href="#">Prodi</a>
                        <a class="collapse-item" href="#">Mata Kuliah</a>
                    </div>
                </div>
            </li>
        @endif

        @if($role === 'dosen')
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Sesi Perkuliahan</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Presensi Dosen</span></a>
            </li>
        @endif

        @if($role === 'mahasiswa')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('krs.index') }}">
                    <i class="fas fa-fw fa-edit"></i>
                    <span>Pengambilan KRS</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-user-check"></i>
                    <span>Presensi Mahasiswa</span></a>
            </li>
        @endif
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
