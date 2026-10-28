<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data HAKI - HAKIKU</title>
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

    <!-- Header Banner -->
    <div class="card admin-banner p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">Kelola Data HAKI</h2>
                <p class="text-white-50 mb-0">Daftar seluruh data Kekayaan Intelektual yang terdaftar di dalam sistem.</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Area Pencarian & Filter -->
    <div class="card card-custom p-4 mb-4">
        <form method="GET" action="{{ route('admin.haki.index') }}" class="row g-3 align-items-end">
            <!-- 1. Input Pencarian -->
            <div class="col-md-5">
                <label for="search" class="form-label small fw-semibold text-secondary">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" 
                           id="search"
                           name="search" 
                           class="form-control bg-light border-start-0 ps-0" 
                           placeholder="Cari judul, nomor sertifikat, nama, atau NIM/NIP..." 
                           value="{{ $search }}">
                </div>
            </div>

            <!-- 2. Dropdown Jenis HAKI -->
            <div class="col-md-3">
                <label for="jenis" class="form-label small fw-semibold text-secondary">Jenis HAKI</label>
                <select id="jenis" name="jenis" class="form-select bg-light">
                    <option value="">Semua Jenis HAKI</option>
                    @foreach($jenisOptions as $option)
                        <option value="{{ $option }}" {{ $jenis == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Dropdown Tahun -->
            <div class="col-md-2">
                <label for="tahun" class="form-label small fw-semibold text-secondary">Tahun</label>
                <select id="tahun" name="tahun" class="form-select bg-light">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Tombol Cari & 5. Tombol Reset -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-3">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                <a href="{{ route('admin.haki.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data HAKI -->
    <div class="card card-custom p-0 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">No</th>
                        <th scope="col">Judul HAKI</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Nomor Sertifikat</th>
                        <th scope="col">Tanggal Terbit</th>
                        <th scope="col">Tahun</th>
                        <th scope="col">Pemilik</th>
                        <th scope="col">NIM/NIP</th>
                        <th scope="col" class="text-center">Status Publikasi</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hakis as $haki)
                        <tr>
                            <td class="text-center fw-semibold text-secondary">
                                {{ $hakis->firstItem() + $loop->index }}
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $haki->judul_haki }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    {{ $haki->jenis_haki }}
                                </span>
                            </td>
                            <td class="text-secondary small">
                                {{ $haki->nomor_sertifikat ?? '-' }}
                            </td>
                            <td class="text-secondary small">
                                {{ $haki->tanggal_terbit ? $haki->tanggal_terbit->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-secondary small">
                                {{ $haki->tahun ?? '-' }}
                            </td>
                            <td class="fw-semibold text-secondary">
                                {{ $haki->user->name ?? '-' }}
                            </td>
                            <td class="text-secondary small">
                                {{ $haki->user->identity_number ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($haki->is_public)
                                    <span class="badge bg-success px-2 py-1 fs-7">Publik</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1 fs-7">Privat</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.haki.show', $haki) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada data HAKI yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($hakis->hasPages())
        <div class="d-flex justify-content-between align-items-center">
            <div class="small text-secondary">
                Menampilkan {{ $hakis->firstItem() }} sampai {{ $hakis->lastItem() }} dari {{ $hakis->total() }} data HAKI
            </div>
            <div>
                {{ $hakis->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

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
