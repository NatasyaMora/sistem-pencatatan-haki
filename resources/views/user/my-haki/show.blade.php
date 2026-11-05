<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail HAKI - {{ $haki->judul_haki }} - HAKIKU</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        .user-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            border-radius: 1rem;
            border: none;
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
            margin-bottom: 0.35rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
        }

        .badge-jenis {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2"
               href="{{ url('/') }}">
                <i class="bi bi-shield-check fs-3"></i>
                <span>HAKIKU</span>
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
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ url('/') }}">
                            Dashboard Publik
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active fw-semibold"
                           href="{{ route('user.haki.index') }}">
                            HAKI Saya
                        </a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 py-2 py-lg-0">
                    <span class="small fw-semibold text-secondary">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ $user->name }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
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

        <!-- Flash Notification: Error -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Flash Notification: Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header Detail Banner -->
        <div class="card user-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge badge-jenis px-3 py-2 rounded-pill">
                            {{ $haki->jenis_haki }}
                        </span>
                        @if($haki->status === 'aktif')
                            <span class="badge bg-success bg-opacity-25 text-white border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-25 text-white border border-secondary-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-x-circle me-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1 text-white">{{ $haki->judul_haki }}</h2>
                    <p class="text-white-50 mb-0">Nomor Sertifikat: <strong>{{ $haki->nomor_sertifikat }}</strong></p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.haki.renewal.create', $haki->id) }}"
                       class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i> Ajukan Pembaruan
                    </a>
                    <a href="{{ route('user.haki.index') }}"
                       class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke HAKI Saya
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Information Card -->
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4 text-primary pb-3 border-bottom d-flex align-items-center justify-content-between">
                <span>
                    <i class="bi bi-journal-check me-2"></i>Rincian Informasi HAKI
                </span>
                <div>
                    @if($haki->is_public)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill fs-7">
                            <i class="bi bi-globe me-1"></i> Dipublikasikan
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fs-7">
                            <i class="bi bi-eye-slash me-1"></i> Tidak Dipublikasikan
                        </span>
                    @endif
                </div>
            </h5>

            <div class="row g-4">
                <!-- 1. Judul HAKI -->
                <div class="col-12">
                    <div class="info-label">Judul HAKI</div>
                    <div class="info-value fs-5 text-dark">{{ $haki->judul_haki }}</div>
                </div>

                <!-- 2. Jenis HAKI -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Jenis HAKI</div>
                    <div class="info-value">
                        <span class="badge badge-jenis px-3 py-2 rounded-2">
                            {{ $haki->jenis_haki }}
                        </span>
                    </div>
                </div>

                <!-- 3. Nomor Sertifikat -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Nomor Sertifikat</div>
                    <div class="info-value font-monospace text-secondary">
                        {{ $haki->nomor_sertifikat }}
                    </div>
                </div>

                <!-- 4. Tahun -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Tahun</div>
                    <div class="info-value text-secondary">
                        {{ $haki->tahun }}
                    </div>
                </div>

                <!-- 5. Tanggal Terbit -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Tanggal Terbit</div>
                    <div class="info-value text-secondary">
                        {{ $haki->tanggal_terbit ? $haki->tanggal_terbit->format('d F Y') : '-' }}
                    </div>
                </div>

                <!-- 6. Tanggal Kedaluwarsa -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Tanggal Kedaluwarsa</div>
                    <div class="info-value text-secondary">
                        {{ $haki->tanggal_kadaluarsa ? $haki->tanggal_kadaluarsa->format('d F Y') : '-' }}
                    </div>
                </div>

                <!-- 7. Status HAKI -->
                <div class="col-md-6 col-lg-4">
                    <div class="info-label">Status HAKI</div>
                    <div class="info-value">
                        @if($haki->status === 'aktif')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-x-circle me-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 8. Fakultas -->
                <div class="col-md-6">
                    <div class="info-label">Fakultas</div>
                    <div class="info-value text-secondary">
                        {{ $haki->faculty->nama ?? '-' }}
                    </div>
                </div>

                <!-- 9. Program Studi / Jurusan -->
                <div class="col-md-6">
                    <div class="info-label">Program Studi / Jurusan</div>
                    <div class="info-value text-secondary">
                        {{ $haki->studyProgram->nama ?? '-' }}
                    </div>
                </div>

                <!-- 10. Status Publikasi -->
                <div class="col-12">
                    <div class="info-label">Status Publikasi</div>
                    <div class="info-value">
                        @if($haki->is_public)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-globe me-1"></i> Dipublikasikan di Dashboard Publik
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-eye-slash me-1"></i> Tidak Dipublikasikan (Hanya Pemilik & Admin)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 11. Deskripsi -->
                <div class="col-12">
                    <div class="info-label">Deskripsi</div>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $haki->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-secondary small">
            <p class="mb-0">
                &copy; {{ date('Y') }} HAKIKU — Portal Pencatatan HAKI Kampus.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
