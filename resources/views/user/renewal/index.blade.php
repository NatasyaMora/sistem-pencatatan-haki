<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembaruan HAKI - HAKIKU</title>

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
            background: #f8fafc;
            color: #1e293b;
        }

        .navbar-custom {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .page-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .table th,
        .table td {
            white-space: nowrap;
            vertical-align: middle;
            font-size: .875rem;
        }

        .empty-state {
            padding: 3.5rem 1rem;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary"
           href="{{ url('/') }}">
            <i class="bi bi-shield-check me-2"></i>HAKIKU
        </a>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('user.haki.index') }}"
               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-folder me-1"></i> HAKI Saya
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

    <div class="mb-4">
        <div class="text-primary fw-semibold small mb-2">
            HAKI SAYA / RIWAYAT PEMBARUAN
        </div>

        <h2 class="fw-bold mb-2">Riwayat Pengajuan Pembaruan</h2>

        <p class="text-secondary mb-0">
            Pantau status dan keputusan Admin atas pengajuan pembaruan HAKI kamu.
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card page-card">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">Daftar Riwayat</h5>
            <p class="text-secondary small mb-0">
                Jumlah pengajuan: {{ $renewalRequests->total() }}
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Judul HAKI</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Masa Berlaku Baru</th>
                        <th>Status</th>
                        <th>Tanggal Diproses</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($renewalRequests as $index => $renewalRequest)
                        <tr>
                            <td class="ps-4">
                                {{ $renewalRequests->firstItem() + $index }}
                            </td>

                            <td>
                                {{ $renewalRequest->haki?->judul_haki
                                    ?? 'HAKI tidak ditemukan' }}
                            </td>

                            <td>
                                {{ $renewalRequest->created_at
                                    ? $renewalRequest->created_at->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $renewalRequest->tanggal_kadaluarsa_baru
                                    ? \Carbon\Carbon::parse($renewalRequest->tanggal_kadaluarsa_baru)->format('d/m/Y')
                                    : '-' }}
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

                            <td>
                                {{ $renewalRequest->diproses_pada
                                    ? \Carbon\Carbon::parse($renewalRequest->diproses_pada)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td style="white-space: normal; min-width: 200px;">
                                {{ $renewalRequest->catatan_admin
                                    ?: 'Belum ada catatan Admin.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center empty-state">
                                <i class="bi bi-inbox fs-1 text-secondary d-block mb-3"></i>

                                <h6 class="fw-bold">
                                    Belum Ada Riwayat Pengajuan
                                </h6>

                                <p class="text-secondary small mb-3">
                                    Pengajuan pembaruan yang kamu kirim akan muncul di halaman ini.
                                </p>

                                <a href="{{ route('user.haki.index') }}"
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Kembali ke HAKI Saya
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($renewalRequests->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $renewalRequests->links() }}
            </div>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('user.haki.index') }}"
           class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke HAKI Saya
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
