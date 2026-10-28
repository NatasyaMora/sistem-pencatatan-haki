<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HAKIKU</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .admin-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        .quick-action {
            min-width: 190px;
        }

        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                padding-top: 0.65rem;
                padding-bottom: 0.65rem;
            }

            .navbar-custom .navbar-collapse {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .navbar-logout {
                margin-top: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid #e2e8f0;
            }

            .quick-action {
                min-width: auto;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">

        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-shield-check fs-3"></i>
            <span>HAKIKU</span>
            <span class="badge bg-danger rounded-pill fs-6 fw-normal ms-1">
                Admin
            </span>
        </a>

        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Menu Navigasi -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ url('/') }}">
                        Dashboard Publik
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active fw-semibold"
                       href="{{ route('admin.dashboard') }}">
                        Dashboard Admin
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-secondary"
                       href="{{ route('admin.jenis-haki.index') }}">
                        Jenis HAKI
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-secondary"
                       href="{{ route('admin.fakultas.index') }}">
                        Fakultas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-secondary"
                       href="{{ route('admin.program-studi.index') }}">
                        Program Studi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-secondary"
                       href="{{ route('admin.haki.index') }}">
                        Kelola HAKI
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-primary fw-semibold"
                       href="{{ route('admin.renewal-requests.index') }}">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        Pengajuan Pembaruan
                    </a>
                </li>
            </ul>

            <!-- Tombol Logout saja, tanpa nama Admin -->
            <div class="navbar-logout d-flex align-items-center">
                <form action="{{ route('logout') }}"
                      method="POST"
                      class="d-inline">
                    @csrf

                    <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="container my-4">

    <!-- Flash Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    <!-- Banner Header Admin -->
    <div class="card admin-banner p-4 p-md-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">

            <div>
                <span class="badge bg-danger px-3 py-2 rounded-pill text-uppercase mb-2"
                      style="letter-spacing: 0.5px;">
                    <i class="bi bi-shield-lock me-1"></i>
                    Administrator Portal
                </span>

                <h2 class="fw-bold mb-1">
                    Selamat Datang, {{ $admin->name }}
                </h2>

                <p class="text-white-50 mb-0">
                    Ringkasan statistik dan manajemen Sistem Informasi
                    Pencatatan HAKI Kampus.
                </p>
            </div>

            <!-- Tombol Pintasan -->
            <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">

                <a href="{{ route('admin.haki.index') }}"
                   class="btn btn-primary rounded-pill px-4 py-2 fw-semibold quick-action">
                    <i class="bi bi-sliders me-1"></i>
                    Kelola Data HAKI
                </a>

                <a href="{{ route('admin.renewal-requests.index') }}"
                   class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold quick-action">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Pengajuan Pembaruan
                </a>

            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <h5 class="fw-bold mb-3 text-secondary">
        <i class="bi bi-bar-chart-line me-2"></i>
        Ringkasan Statistik Sistem
    </h5>

    <div class="row g-4 mb-4">

        <!-- Card 1: Total HAKI -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-journal-check"></i>
                    </div>

                    <div>
                        <div class="text-secondary small fw-semibold">
                            Total Seluruh HAKI
                        </div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($totalHaki) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Dipublikasikan -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-globe2"></i>
                    </div>

                    <div>
                        <div class="text-secondary small fw-semibold">
                            Sudah Dipublikasikan
                        </div>
                        <h3 class="fw-bold mb-0 text-success">
                            {{ number_format($totalPublic) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Belum Dipublikasikan -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-eye-slash"></i>
                    </div>

                    <div>
                        <div class="text-secondary small fw-semibold">
                            Belum Dipublikasikan
                        </div>
                        <h3 class="fw-bold mb-0 text-warning">
                            {{ number_format($totalNonPublic) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Mahasiswa Pemilik -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-mortarboard"></i>
                    </div>

                    <div>
                        <div class="text-secondary small fw-semibold">
                            Mahasiswa Pemilik HAKI
                        </div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($totalMahasiswaPemilik) }}
                            <span class="fs-6 fw-normal text-muted">User</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Dosen Pemilik -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon"
                         style="color: #8b5cf6; background-color: rgba(139, 92, 246, 0.1);">
                        <i class="bi bi-person-workspace"></i>
                    </div>

                    <div>
                        <div class="text-secondary small fw-semibold">
                            Dosen Pemilik HAKI
                        </div>
                        <h3 class="fw-bold mb-0">
                            {{ number_format($totalDosenPemilik) }}
                            <span class="fs-6 fw-normal text-muted">User</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 6: Sebaran Jenis HAKI -->
        <div class="col-md-4">
            <div class="card stat-card p-4 h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-tags"></i>
                    </div>

                    <div class="w-100">
                        <div class="text-secondary small fw-semibold mb-2">
                            Sebaran Jenis HAKI
                        </div>

                        <div class="d-flex flex-wrap gap-1">
                            @forelse($jenisStats as $jenis => $total)
                                <span class="badge bg-light text-dark border p-2 mb-1">
                                    {{ $jenis }}:
                                    <strong class="text-primary">{{ $total }}</strong>
                                </span>
                            @empty
                                <span class="text-muted small">
                                    Belum ada data HAKI
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center text-secondary small">
        <p class="mb-0">
            &copy; {{ date('Y') }} HAKIKU —
            Administrator Portal Pencatatan HAKI Kampus.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
