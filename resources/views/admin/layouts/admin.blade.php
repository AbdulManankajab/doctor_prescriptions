<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') | Doctor Prescription System</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <i class="fas fa-heartbeat brand-image"></i>
            <span class="brand-text font-weight-light">Doctor Prescription</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::guard('admin')->user()->name }}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.patients.index') }}" class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Patients</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.prescriptions.index') }}" class="nav-link {{ request()->routeIs('admin.prescriptions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-prescription"></i>
                            <p>Prescriptions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.doctors.index') }}" class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.facilities.index') }}" class="nav-link {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hospital"></i>
                            <p>Facilities</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.pharmacy.index') }}" class="nav-link {{ request()->routeIs('admin.pharmacy.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-capsules"></i>
                            <p>Pharmacy Users</p>
                        </a>
                    </li>
                    <li class="nav-header">SYSTEM MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.medicines.index') }}" class="nav-link {{ request()->routeIs('admin.medicines.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-pills"></i>
                            <p>Medicines</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.defaults.index') }}" class="nav-link {{ request()->routeIs('admin.defaults.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-sticky-note"></i>
                            <p>Default Notes</p>
                        </a>
                    </li>
                    <li class="nav-header">LINKS</li>
                    <li class="nav-item">
                        <a href="{{ route('doctor.dashboard') }}" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-user-md text-primary"></i>
                            <p>Doctor Portal</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pharmacy.dashboard') }}" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-capsules text-teal"></i>
                            <p>Pharmacy Portal</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2026 Doctor Prescription System.</strong>
        All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@yield('scripts')
</body>
</html>
