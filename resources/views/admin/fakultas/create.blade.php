<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Fakultas - HAKIKU</title>
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
        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ url('/') }}">
            <i class="bi bi-shield-check fs-3"></i>
            <span>HAKIKU</span>
            <span class="badge bg-danger rounded-pill fs-6 fw-normal ms-1">Admin</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ url('/') }}">Dashboard Publik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ route('admin.jenis-haki.index') }}">Jenis HAKI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="{{ route('admin.fakultas.index') }}">Fakultas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ route('admin.program-studi.index') }}">Program Studi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ route('admin.haki.index') }}">Kelola HAKI</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="small fw-semibold text-secondary">
                    <i class="bi bi-person-badge-fill text-danger me-1"></i> {{ auth()->user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="container my-4">

    <!-- Header Banner -->
    <div class="card admin-banner p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">Tambah Fakultas</h2>
                <p class="text-white-50 mb-0">Masukkan nama Fakultas baru ke dalam sistem.</p>
            </div>
            <div>
                <a href="{{ route('admin.fakultas.index') }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5">
                <form action="{{ route('admin.fakultas.store') }}" method="POST">
                    @csrf

                    <!-- Nama Fakultas -->
                    <div class="mb-4">
                        <label for="nama" class="form-label fw-semibold">Nama Fakultas <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nama" 
                               id="nama" 
                               class="form-control form-control-lg @error('nama') is-invalid @enderror" 
                               placeholder="Masukkan nama Fakultas..." 
                               value="{{ old('nama') }}" 
                               required 
                               autofocus>
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Informational Note -->
                    <div class="alert alert-info border-0 rounded-3 mb-4 small">
                        <i class="bi bi-info-circle-fill me-2"></i> Status master Fakultas yang baru dibuat akan otomatis diset sebagai <strong>Aktif</strong>.
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Fakultas
                        </button>
                        <a href="{{ route('admin.fakultas.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<footer class="mt-auto py-3 bg-white text-center border-top">
    <div class="container text-muted small">
        &copy; {{ date('Y') }} HAKIKU — Sistem Informasi Pencatatan Hak Kekayaan Intelektual
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
