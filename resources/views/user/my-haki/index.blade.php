<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HAKI Saya - HAKIKU</title>

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

        .user-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            border-radius: 1rem;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
        }

        .table th {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .user-card .identity-info span {
                display: block;
                margin-bottom: 0.35rem;
            }

            .section-header {
                align-items: stretch !important;
            }

            .action-buttons a {
                flex: 1 1 auto;
                text-align: center;
            }
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
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4"
                 role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        <!-- Flash Notification: Success -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4"
                 role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        <!-- Kartu Identitas Pengguna -->
        <div class="card user-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <span class="badge bg-primary px-3 py-2 rounded-pill text-uppercase mb-2"
                          style="letter-spacing: 0.5px;">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ $user->user_type ?? 'User' }}
                    </span>

                    <h3 class="fw-bold mb-1">{{ $user->name }}</h3>

                    <p class="mb-0 text-white-50 identity-info">
                        <span class="me-3">
                            <i class="bi bi-card-text me-1"></i>
                            NIM/NIP:
                            <strong>{{ $user->identity_number ?? '-' }}</strong>
                        </span>

                        <span>
                            <i class="bi bi-envelope me-1"></i>
                            {{ $user->email }}
                        </span>
                    </p>
                </div>

                <div class="col-md-4 text-md-end">
                    <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-center"
                         style="min-width: 140px;">
                        <div class="small text-white-50">Total HAKI Saya</div>
                        <div class="fs-2 fw-bold">{{ $hakis->total() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Header & Tombol Aksi -->
        <div class="section-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="fw-bold mb-0">Daftar HAKI Saya</h4>
                <p class="small text-secondary mb-0">
                    Riwayat data pencatatan HAKI milik Anda yang telah terdaftar di sistem.
                </p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('user.haki.renewal-history.index') }}"
                   class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-clock-history me-1"></i>
                    Riwayat Pembaruan
                </a>

                <a href="{{ route('user.haki.create') }}"
                   class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah HAKI
                </a>
            </div>
        </div>

        <!-- Tabel Data HAKI Saya -->
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
                            <th scope="col">Tanggal Kedaluwarsa</th>
                            <th scope="col" class="text-center">Status HAKI</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($hakis as $index => $haki)
                            <tr>
                                <td class="ps-4 fw-semibold text-secondary">
                                    {{ $hakis->firstItem() + $index }}
                                </td>

                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $haki->judul_haki }}
                                    </div>

                                    @if($haki->deskripsi)
                                        <div class="small text-secondary text-truncate"
                                             style="max-width: 380px;">
                                            {{ $haki->deskripsi }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge badge-jenis px-2 py-2 rounded-2">
                                        {{ $haki->jenis_haki }}
                                    </span>
                                </td>

                                <td class="font-monospace text-secondary small">
                                    {{ $haki->nomor_sertifikat }}
                                </td>

                                <td class="text-secondary small">
                                    {{ $haki->tanggal_terbit
                                        ? $haki->tanggal_terbit->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td class="text-secondary small">
                                    {{ $haki->tanggal_kadaluarsa
                                        ? $haki->tanggal_kadaluarsa->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td class="text-center">
                                    @if($haki->status === 'aktif')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi: Tombol Detail & Tombol Ajukan Pembaruan -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ route('user.haki.show', $haki->id) }}"
                                           class="btn btn-sm btn-outline-info rounded-pill text-nowrap px-3">
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>

                                        <a href="{{ route('user.haki.renewal.create', $haki->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill text-nowrap px-3">
                                            <i class="bi bi-arrow-repeat me-1"></i>
                                            Ajukan Pembaruan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-folder2-open fs-1 text-secondary opacity-50 d-block mb-2"></i>

                                        <h5 class="fw-semibold text-secondary mb-1">
                                            Belum ada data HAKI.
                                        </h5>

                                        <p class="small text-muted mb-3">
                                            Anda belum memiliki data pencatatan HAKI di akun ini.
                                        </p>

                                        <a href="{{ route('user.haki.create') }}"
                                           class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-plus-lg me-1"></i>
                                            Catat HAKI Baru Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hakis->hasPages())
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $hakis->links() }}
                </div>
            @endif
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
