<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Master Program Studi - HAKIKU</title>
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
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
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
                    <a class="nav-link text-secondary" href="{{ route('admin.fakultas.index') }}">Fakultas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="{{ route('admin.program-studi.index') }}">Program Studi</a>
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
                <h2 class="fw-bold mb-1">Kelola Master Program Studi</h2>
                <p class="text-white-50 mb-0">Kelola master data Program Studi beserta Fakultas pengampunya.</p>
            </div>
            <div>
                <a href="{{ route('admin.program-studi.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Program Studi
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Data Program Studi -->
    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 5%;">No</th>
                        <th scope="col">Nama Program Studi</th>
                        <th scope="col" style="width: 25%;">Fakultas</th>
                        <th scope="col" style="width: 15%;">Status</th>
                        <th scope="col" style="width: 25%;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($studyPrograms as $index => $studyProgram)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-dark">{{ $studyProgram->nama }}</td>
                            <td>
                                <span class="fw-medium text-secondary">
                                    <i class="bi bi-building me-1"></i> {{ $studyProgram->faculty->nama ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($studyProgram->status === 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.program-studi.edit', $studyProgram) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>

                                <form action="{{ route('admin.program-studi.toggle-status', $studyProgram) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-sm {{ $studyProgram->status === 'aktif' ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3"
                                            onclick="return confirm('Apakah Anda yakin ingin mengubah status data ini?')">
                                        <i class="bi {{ $studyProgram->status === 'aktif' ? 'bi-slash-circle' : 'bi-check-circle' }} me-1"></i>
                                        {{ $studyProgram->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-3 text-secondary"></i>
                                    <h5 class="fw-bold">Belum Ada Data Program Studi</h5>
                                    <p class="mb-3">Master data Program Studi belum dikonfigurasi. Klik tombol di bawah untuk menambahkan Program Studi baru.</p>
                                    <a href="{{ route('admin.program-studi.create') }}" class="btn btn-primary rounded-pill px-4">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Program Studi Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
