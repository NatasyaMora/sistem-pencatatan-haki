<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail HAKI - HAKIKU</title>
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
        .info-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
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
                    <a class="nav-link active fw-semibold" href="{{ route('admin.haki.index') }}">Kelola HAKI</a>
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

    <!-- Flash Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="card admin-banner p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">Detail HAKI</h2>
                <p class="text-white-50 mb-0">Informasi lengkap Kekayaan Intelektual dan data pemilik terdaftar.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.haki.edit', $haki) }}" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <a href="{{ route('admin.haki.index') }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Kelola HAKI
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Section Informasi HAKI -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-journal-bookmark-fill me-2"></i>Informasi HAKI
                    </h5>
                    <div>
                        @if($haki->is_public)
                            <span class="badge bg-success px-3 py-2 fs-6">
                                <i class="bi bi-globe2 me-1"></i> Publik
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 fs-6">
                                <i class="bi bi-lock-fill me-1"></i> Privat
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="info-label">Judul HAKI</div>
                        <div class="info-value fs-5 text-dark">{{ $haki->judul_haki }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Jenis HAKI</div>
                        <div class="info-value">
                            <span class="badge bg-light text-dark border p-2 fw-semibold">
                                {{ $haki->jenis_haki }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Nomor Sertifikat</div>
                        <div class="info-value text-secondary">{{ $haki->nomor_sertifikat ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Tanggal Terbit</div>
                        <div class="info-value text-secondary">
                            {{ $haki->tanggal_terbit ? $haki->tanggal_terbit->format('d F Y') : '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Tahun</div>
                        <div class="info-value text-secondary">{{ $haki->tahun ?? '-' }}</div>
                    </div>

                    <div class="col-12">
                        <div class="info-label">Deskripsi</div>
                        <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                            {{ $haki->deskripsi ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Informasi File -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-primary">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Informasi File Sertifikat
                </h5>
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                    <div class="fs-2 text-danger">
                        <i class="bi bi-file-earmark-code"></i>
                    </div>
                    <div>
                        <div class="info-label mb-1">Path / Nama File</div>
                        <div class="fw-semibold text-dark text-break">
                            {{ $haki->file_sertifikat ?? 'Belum ada file sertifikat diunggah.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Informasi Pemilik -->
        <div class="col-lg-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-4 text-primary pb-3 border-bottom">
                    <i class="bi bi-person-lines-fill me-2"></i>Informasi Pemilik
                </h5>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 text-dark">{{ $haki->user->name ?? '-' }}</div>
                        <span class="badge bg-info text-dark fw-semibold">
                            {{ ucfirst($haki->user->user_type ?? 'User') }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="info-label">NIM / NIP</div>
                    <div class="info-value text-secondary">{{ $haki->user->identity_number ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <div class="info-label">Email</div>
                    <div class="info-value text-secondary text-break">{{ $haki->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center text-secondary small">
        <p class="mb-0">&copy; {{ date('Y') }} HAKIKU — Administrator Portal Pencatatan HAKI Kampus.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
