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
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .page-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-weight: 700;
            color: #1e293b;
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

            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('user.haki.index') }}"
                   class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>
                    HAKI Saya
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <!-- Judul Halaman -->
        <div class="mb-4">
            <div class="text-primary fw-semibold small mb-2">
                HAKI SAYA / PEMBARUAN
            </div>

            <h2 class="fw-bold mb-2">Pengajuan Pembaruan HAKI</h2>

            <p class="text-secondary mb-0">
                Lengkapi informasi pembaruan sertifikat HAKI yang kamu miliki.
            </p>
        </div>

        <!-- Pesan Error dan Sukses -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}

                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}

                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali data yang kamu isi:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Informasi HAKI -->
        <div class="card page-card mb-4">
            <div class="card-body p-4">
                <h5 class="section-title mb-4">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                    Informasi HAKI
                </h5>

                <div class="mb-3">
                    <label class="form-label text-secondary">Judul HAKI</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $haki->judul_haki }}"
                           readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">
                            Nomor Sertifikat Saat Ini
                        </label>
                        <input type="text"
                               class="form-control"
                               value="{{ $haki->nomor_sertifikat }}"
                               readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">
                            Masa Berlaku Saat Ini
                        </label>
                        <input type="text"
                               class="form-control"
                               value="{{ $haki->tanggal_kadaluarsa ? \Carbon\Carbon::parse($haki->tanggal_kadaluarsa)->format('d-m-Y') : 'Belum diisi' }}"
                               readonly>
                    </div>
                </div>

                <div>
                    <label class="form-label text-secondary">Status HAKI</label>
                    <div>
                        @if ($haki->status === 'aktif')
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="bi bi-clock-history me-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pembaruan -->
        @if ($hasPendingRequest)

            <div class="card page-card">
                <div class="card-body p-4">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-hourglass-split me-2"></i>
                        <strong>Pengajuan sedang diproses.</strong>
                        <p class="mb-0 mt-2">
                            Pengajuan pembaruan untuk HAKI ini masih menunggu
                            konfirmasi Admin. Kamu belum dapat mengirim
                            pengajuan baru untuk HAKI yang sama.
                        </p>
                    </div>

                    <a href="{{ route('user.haki.index') }}"
                       class="btn btn-secondary">
                        Kembali ke HAKI Saya
                    </a>
                </div>
            </div>

        @else

            <div class="card page-card">
                <div class="card-body p-4">
                    <h5 class="section-title mb-2">Data Pembaruan</h5>

                    <p class="text-secondary small mb-4">
                        Kolom bertanda <span class="text-danger">*</span>
                        wajib diisi.
                    </p>

                    <form action="{{ route('user.haki.renewal.store', $haki->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="tanggal_kadaluarsa_baru"
                                   class="form-label">
                                Masa Berlaku Baru
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="tanggal_kadaluarsa_baru"
                                   id="tanggal_kadaluarsa_baru"
                                   class="form-control @error('tanggal_kadaluarsa_baru') is-invalid @enderror"
                                   value="{{ old('tanggal_kadaluarsa_baru') }}"
                                   min="{{ $haki->tanggal_kadaluarsa && \Carbon\Carbon::parse($haki->tanggal_kadaluarsa)->addDay()->isAfter(now()) ? \Carbon\Carbon::parse($haki->tanggal_kadaluarsa)->addDay()->format('Y-m-d') : now()->format('Y-m-d') }}"
                                   required>

                            @error('tanggal_kadaluarsa_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Masukkan tanggal berakhir masa berlaku yang baru
                                sesuai informasi sertifikat.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nomor_sertifikat_baru"
                                   class="form-label">
                                Nomor Sertifikat Baru
                            </label>

                            <input type="text"
                                   name="nomor_sertifikat_baru"
                                   id="nomor_sertifikat_baru"
                                   class="form-control @error('nomor_sertifikat_baru') is-invalid @enderror"
                                   value="{{ old('nomor_sertifikat_baru') }}"
                                   maxlength="255"
                                   placeholder="Kosongkan jika tidak berubah">

                            @error('nomor_sertifikat_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="file_sertifikat_baru"
                                   class="form-label">
                                Unggah Sertifikat Baru
                            </label>

                            <input type="file"
                                   name="file_sertifikat_baru"
                                   id="file_sertifikat_baru"
                                   class="form-control @error('file_sertifikat_baru') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png">

                            @error('file_sertifikat_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Format PDF, JPG, JPEG, atau PNG.
                                Ukuran maksimal 3 MB. Kosongkan jika tidak ada
                                berkas sertifikat baru.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label">
                                Catatan Tambahan
                            </label>

                            <textarea name="catatan"
                                      id="catatan"
                                      rows="3"
                                      class="form-control @error('catatan') is-invalid @enderror"
                                      placeholder="Tambahkan keterangan jika diperlukan">{{ old('catatan') }}</textarea>

                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Pengajuan akan diperiksa oleh Admin. Data HAKI lama
                            tidak berubah sebelum pengajuan dikonfirmasi.
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send me-1"></i>
                                Kirim Pengajuan
                            </button>

                            <a href="{{ route('user.haki.index') }}"
                               class="btn btn-outline-secondary px-4">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-secondary small">
            &copy; {{ date('Y') }} HAKIKU — Portal Pencatatan HAKI Kampus.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
