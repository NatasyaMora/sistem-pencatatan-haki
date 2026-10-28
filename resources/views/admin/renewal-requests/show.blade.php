<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan HAKI - HAKIKU</title>

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

        .detail-label {
            color: #64748b;
            font-size: .875rem;
            margin-bottom: .35rem;
        }

        .detail-value {
            font-weight: 600;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check me-2"></i>HAKIKU
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <!-- Judul -->
        <div class="mb-4">
            <a href="{{ route('admin.renewal-requests.index') }}"
               class="text-decoration-none text-secondary small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Daftar Pengajuan
            </a>

            <h2 class="fw-bold mt-3 mb-2">Detail Pengajuan Pembaruan</h2>
            <p class="text-secondary mb-0">
                Periksa informasi pengajuan sebelum mengambil keputusan.
            </p>
        </div>

        <!-- Notifikasi -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Pengajuan belum dapat diproses:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Status Pengajuan -->
        <div class="card page-card mb-4">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="text-secondary small mb-1">Status Pengajuan</div>

                    @if ($renewalRequest->status === 'menunggu')
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="bi bi-hourglass-split me-1"></i> Menunggu
                        </span>
                    @elseif ($renewalRequest->status === 'dikonfirmasi')
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i> Dikonfirmasi
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i> Ditolak
                        </span>
                    @endif
                </div>

                <div class="text-secondary small">
                    ID Pengajuan: #{{ $renewalRequest->id }}
                </div>
            </div>
        </div>

        <!-- Informasi Pemilik -->
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-person-badge text-primary me-2"></i>
                    Informasi Pemilik
                </h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-label">Nama</div>
                        <div class="detail-value">
                            {{ $renewalRequest->user?->name ?? 'Pengguna tidak ditemukan' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">NIM/NIP</div>
                        <div class="detail-value">
                            {{ $renewalRequest->user?->identity_number ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">
                            {{ $renewalRequest->user?->email ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Tanggal Pengajuan</div>
                        <div class="detail-value">
                            {{ $renewalRequest->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi HAKI dan Pembaruan -->
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                    Informasi HAKI
                </h5>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="detail-label">Judul HAKI</div>
                        <div class="detail-value">
                            {{ $renewalRequest->haki?->judul_haki ?? 'HAKI tidak ditemukan' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Nomor Sertifikat Saat Ini</div>
                        <div class="detail-value">
                            {{ $renewalRequest->haki?->nomor_sertifikat ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Nomor Sertifikat Baru</div>
                        <div class="detail-value">
                            {{ $renewalRequest->nomor_sertifikat_baru ?: 'Tidak ada perubahan' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Masa Berlaku Saat Ini</div>
                        <div class="detail-value">
                            @if ($renewalRequest->haki?->tanggal_kadaluarsa)
                                {{ \Carbon\Carbon::parse($renewalRequest->haki->tanggal_kadaluarsa)->format('d/m/Y') }}
                            @else
                                Belum diisi
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Masa Berlaku yang Diajukan</div>
                        <div class="detail-value text-primary">
                            {{ \Carbon\Carbon::parse($renewalRequest->tanggal_kadaluarsa_baru)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-label">Sertifikat Baru</div>
                        <div class="detail-value">
                            @if ($renewalRequest->file_sertifikat_baru)
                                <span class="text-success">
                                    <i class="bi bi-file-earmark-check me-1"></i>
                                    Berkas tersedia
                                </span>
                                <div class="small text-secondary fw-normal mt-1">
                                    Berkas tidak ditampilkan melalui tautan publik.
                                </div>
                            @else
                                Tidak ada berkas baru
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="detail-label">Catatan dari Pengguna</div>
                        <div class="p-3 bg-light rounded-3">
                            {{ $renewalRequest->catatan ?: 'Tidak ada catatan tambahan.' }}
                        </div>
                    </div>

                    @if ($renewalRequest->status !== 'menunggu')
                        <div class="col-md-6">
                            <div class="detail-label">Tanggal Diproses</div>
                            <div class="detail-value">
                                {{ $renewalRequest->diproses_pada?->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="detail-label">Catatan Admin</div>
                            <div class="p-3 bg-light rounded-3">
                                {{ $renewalRequest->catatan_admin ?: 'Tidak ada catatan dari Admin.' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aksi Admin -->
        @if ($renewalRequest->status === 'menunggu'
            && $renewalRequest->haki
            && $renewalRequest->user)

            <div class="card page-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Keputusan Admin</h5>
                    <p class="text-secondary small mb-4">
                        Pastikan informasi pengajuan sudah diperiksa.
                        Keputusan yang dikirim akan tercatat pada sistem.
                    </p>

                    <div class="row g-4">

                        <!-- Konfirmasi -->
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Konfirmasi Pengajuan
                                </h6>

                                <p class="text-secondary small">
                                    Data HAKI lama akan diperbarui dengan
                                    informasi yang diajukan.
                                </p>

                                <form action="{{ route('admin.renewal-requests.confirm', $renewalRequest->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Konfirmasi pengajuan ini? Data HAKI akan diperbarui.');">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="catatan_konfirmasi"
                                               class="form-label">
                                            Catatan Admin (opsional)
                                        </label>

                                        <textarea name="catatan_admin"
                                                  id="catatan_konfirmasi"
                                                  class="form-control"
                                                  rows="3"
                                                  maxlength="1000"
                                                  placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan_admin') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Konfirmasi Pembaruan
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Penolakan -->
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Tolak Pengajuan
                                </h6>

                                <p class="text-secondary small">
                                    Berikan alasan penolakan agar pengguna
                                    mengetahui keputusan Admin.
                                </p>

                                <form action="{{ route('admin.renewal-requests.reject', $renewalRequest->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menolak pengajuan ini?');">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="catatan_penolakan"
                                               class="form-label">
                                            Alasan Penolakan
                                            <span class="text-danger">*</span>
                                        </label>

                                        <textarea name="catatan_admin"
                                                  id="catatan_penolakan"
                                                  class="form-control"
                                                  rows="3"
                                                  maxlength="1000"
                                                  required
                                                  placeholder="Tuliskan alasan penolakan">{{ old('catatan_admin') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-x-lg me-1"></i>
                                        Tolak Pengajuan
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        @endif

        <a href="{{ route('admin.renewal-requests.index') }}"
           class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Daftar Pengajuan
        </a>

    </main>

    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-secondary small">
            &copy; {{ date('Y') }} HAKIKU — Portal Pencatatan HAKI Kampus.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
