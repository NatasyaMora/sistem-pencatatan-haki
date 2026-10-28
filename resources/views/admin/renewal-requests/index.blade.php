<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Pembaruan HAKI - HAKIKU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .page-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .table th {
            white-space: nowrap;
            font-size: .875rem;
        }

        .table td {
            font-size: .875rem;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check me-2"></i>HAKIKU
            </a>

            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <!-- Judul Halaman -->
        <div class="mb-4">
            <div class="text-primary small fw-semibold mb-2">
                ADMIN / PENGELOLAAN HAKI
            </div>

            <h2 class="fw-bold mb-2">Pengajuan Pembaruan HAKI</h2>

            <p class="text-secondary mb-0">
                Kelola dan tinjau pengajuan pembaruan masa berlaku sertifikat HAKI.
            </p>
        </div>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ada kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Status -->
        <div class="card page-card mb-4">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('admin.renewal-requests.index') }}"
                      method="GET"
                      class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="status" class="form-label fw-semibold">
                            Filter Status Pengajuan
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu"
                                {{ $status === 'menunggu' ? 'selected' : '' }}>
                                Menunggu
                            </option>
                            <option value="dikonfirmasi"
                                {{ $status === 'dikonfirmasi' ? 'selected' : '' }}>
                                Dikonfirmasi
                            </option>
                            <option value="ditolak"
                                {{ $status === 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> Terapkan
                        </button>
                    </div>

                    <div class="col-auto">
                        <a href="{{ route('admin.renewal-requests.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Pengajuan -->
        <div class="card page-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-1">Daftar Pengajuan</h5>
                <p class="text-secondary small mb-0">
                    Jumlah pengajuan sesuai filter: {{ $renewalRequests->total() }}
                </p>
            </div>

            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No.</th>
                                <th>Pemilik HAKI</th>
                                <th>Judul HAKI</th>
                                <th>Masa Berlaku Lama</th>
                                <th>Masa Berlaku Baru</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($renewalRequests as $index => $renewalRequest)
                                <tr>
                                    <td class="ps-4">
                                        {{ $renewalRequests->firstItem() + $index }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $renewalRequest->user?->name ?? 'Pengguna tidak ditemukan' }}
                                        </div>
                                        <div class="small text-secondary">
                                            {{ $renewalRequest->user?->identity_number ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $renewalRequest->haki?->judul_haki ?? 'HAKI tidak ditemukan' }}
                                    </td>

                                    <td>
                                        @if ($renewalRequest->haki?->tanggal_kadaluarsa)
                                            {{ \Carbon\Carbon::parse($renewalRequest->haki->tanggal_kadaluarsa)->format('d/m/Y') }}
                                        @else
                                            <span class="text-secondary">Belum diisi</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($renewalRequest->tanggal_kadaluarsa_baru)->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        @if ($renewalRequest->status === 'menunggu')
                                            <span class="badge bg-warning text-dark">
                                                Menunggu
                                            </span>
                                        @elseif ($renewalRequest->status === 'dikonfirmasi')
                                            <span class="badge bg-success">
                                                Dikonfirmasi
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center pe-4">
                                        <a href="{{ route('admin.renewal-requests.show', $renewalRequest->id) }}"
                                           class="btn btn-sm btn-outline-primary text-nowrap">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-secondary d-block mb-3"></i>

                                        <h6 class="fw-bold">Belum ada pengajuan pembaruan</h6>

                                        <p class="text-secondary small mb-0">
                                            Pengajuan dari mahasiswa atau dosen akan muncul di halaman ini.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($renewalRequests->hasPages())
                <div class="card-footer bg-white border-0 px-4 py-3">
                    {{ $renewalRequests->links() }}
                </div>
            @endif
        </div>

        <div class="mt-3">
            <a href="{{ route('admin.haki.index') }}"
               class="text-decoration-none text-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Data HAKI
            </a>
        </div>

    </main>

    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-secondary small">
            &copy; {{ date('Y') }} HAKIKU — Portal Pencatatan HAKI Kampus.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
