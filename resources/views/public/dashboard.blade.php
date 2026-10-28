<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Publik - HAKIKU (Pencatatan HAKI Kampus)</title>
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
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 3.5rem 0 3rem;
            border-bottom-right-radius: 2rem;
            border-bottom-left-radius: 2rem;
        }
        .stat-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
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
        }
        .table-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .badge-jenis {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 0.5rem;
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
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="{{ url('/') }}">Dashboard Publik</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary rounded-pill px-3">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                        </a>
                    @else
                        <a href="{{ url('/my-haki') }}" class="btn btn-primary rounded-pill px-3">
                            <i class="bi bi-folder-check me-1"></i> HAKI Saya
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section mb-5">
    <div class="container text-center">
        <h1 class="fw-bold mb-3">Sistem Informasi Pencatatan HAKI Kampus</h1>
        <p class="lead text-white-50 max-w-2xl mx-auto">
            Portal resmi informasi publikasi Hak Kekayaan Intelektual yang dimiliki oleh Civitas Akademika.
        </p>
    </div>
</header>

<!-- Main Content -->
<main class="container mb-5">

    <!-- Section Statistik -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Total HAKI Tercatat</div>
                        <h3 class="fw-bold mb-0">{{ number_format($totalTercatat) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">HAKI Dipublikasikan</div>
                        <h3 class="fw-bold mb-0">{{ number_format($totalPublic) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Kategori Publik</div>
                        <div class="small text-muted mt-1">
                            @forelse($jenisStats as $jenis => $count)
                                <span class="badge bg-light text-dark border me-1 mb-1">{{ $jenis }}: {{ $count }}</span>
                            @empty
                                <span class="text-muted">Belum ada kategori</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Daftar HAKI Publik -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Daftar HAKI Publik</h4>
        <span class="badge bg-secondary rounded-pill px-3 py-2">Total: {{ $publicHakis->total() }} Data</span>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4" style="width: 50px;">#</th>
                        <th scope="col">Judul HAKI</th>
                        <th scope="col">Jenis HAKI</th>
                        <th scope="col">Nomor Sertifikat</th>
                        <th scope="col">Tanggal Terbit</th>
                        <th scope="col">Tahun</th>
                        <th scope="col" class="pe-4">Pemilik HAKI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publicHakis as $index => $haki)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">
                                {{ $publicHakis->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $haki->judul_haki }}</div>
                                @if($haki->deskripsi)
                                    <div class="small text-secondary text-truncate" style="max-width: 350px;">
                                        {{ $haki->deskripsi }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-jenis">
                                    {{ $haki->jenis_haki }}
                                </span>
                            </td>
                            <td class="font-monospace text-secondary small">
                                {{ $haki->nomor_sertifikat }}
                            </td>
                            <td class="text-secondary small">
                                {{ $haki->tanggal_terbit ? $haki->tanggal_terbit->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $haki->tahun }}</span>
                            </td>
                            <td class="pe-4">
                                <div class="fw-semibold small text-dark">{{ $haki->user->name ?? '-' }}</div>
                                @if(isset($haki->user->identity_number))
                                    <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                        {{ strtoupper($haki->user->user_type ?? 'USER') }}: {{ $haki->user->identity_number }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-folder-x fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                    <h5 class="fw-semibold text-secondary">Belum ada data HAKI yang dipublikasikan.</h5>
                                    <p class="small text-muted mb-0">Data HAKI akan ditampilkan publik setelah disetujui oleh Admin.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($publicHakis->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4">
                {{ $publicHakis->links() }}
            </div>
        @endif
    </div>

</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center text-secondary small">
        <p class="mb-0">&copy; {{ date('Y') }} HAKIKU — Sistem Informasi Pencatatan Hak Kekayaan Intelektual Kampus.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
