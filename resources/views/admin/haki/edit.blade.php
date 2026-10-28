<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data HAKI - HAKIKU</title>
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
                <h2 class="fw-bold mb-1">Edit Data HAKI</h2>
                <p class="text-white-50 mb-0">Ubah rincian informasi Kekayaan Intelektual terdaftar.</p>
            </div>
            <div>
                <a href="{{ route('admin.haki.show', $haki) }}" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-x-lg me-1"></i> Batal
                </a>
            </div>
        </div>
    </div>

    <!-- General Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan input!</strong> Silakan periksa kembali form di bawah.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Edit Card -->
    <div class="card card-custom p-4 mb-4">
        <form action="{{ route('admin.haki.update', $haki) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 1. Pemilik (READ-ONLY) -->
            <div class="p-3 bg-light rounded-3 mb-4 border">
                <label class="form-label fw-bold text-secondary mb-1">
                    <i class="bi bi-person-lock me-1"></i> Pemilik HAKI (Read-Only)
                </label>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Nama Pemilik:</small>
                        <span class="fw-semibold text-dark">{{ $haki->user->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">NIM / NIP:</small>
                        <span class="fw-semibold text-dark">{{ $haki->user->identity_number ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- 2. Judul HAKI -->
                <div class="col-12">
                    <label for="judul_haki" class="form-label fw-semibold">Judul HAKI <span class="text-danger">*</span></label>
                    <input type="text" 
                           id="judul_haki" 
                           name="judul_haki" 
                           class="form-control @error('judul_haki') is-invalid @enderror" 
                           value="{{ old('judul_haki', $haki->judul_haki) }}" 
                           required>
                    @error('judul_haki')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. Jenis HAKI -->
                <div class="col-md-6">
                    <label for="jenis_haki" class="form-label fw-semibold">Jenis HAKI <span class="text-danger">*</span></label>
                    <select id="jenis_haki" 
                            name="jenis_haki" 
                            class="form-select @error('jenis_haki') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Jenis HAKI --</option>
                        @foreach($jenisOptions as $option)
                            <option value="{{ $option }}" {{ old('jenis_haki', $haki->jenis_haki) == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_haki')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 4. Nomor Sertifikat -->
                <div class="col-md-6">
                    <label for="nomor_sertifikat" class="form-label fw-semibold">Nomor Sertifikat <span class="text-danger">*</span></label>
                    <input type="text" 
                           id="nomor_sertifikat" 
                           name="nomor_sertifikat" 
                           class="form-control @error('nomor_sertifikat') is-invalid @enderror" 
                           value="{{ old('nomor_sertifikat', $haki->nomor_sertifikat) }}" 
                           required>
                    @error('nomor_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 5. Tanggal Terbit -->
                <div class="col-md-6">
                    <label for="tanggal_terbit" class="form-label fw-semibold">Tanggal Terbit <span class="text-danger">*</span></label>
                    <input type="date" 
                           id="tanggal_terbit" 
                           name="tanggal_terbit" 
                           class="form-control @error('tanggal_terbit') is-invalid @enderror" 
                           value="{{ old('tanggal_terbit', $haki->tanggal_terbit ? $haki->tanggal_terbit->format('Y-m-d') : '') }}" 
                           required>
                    @error('tanggal_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 6. Tahun -->
                <div class="col-md-6">
                    <label for="tahun" class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                    <input type="number" 
                           id="tahun" 
                           name="tahun" 
                           min="1900" 
                           max="2100" 
                           class="form-control @error('tahun') is-invalid @enderror" 
                           value="{{ old('tahun', $haki->tahun) }}" 
                           required>
                    @error('tahun')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 7. Deskripsi -->
                <div class="col-12">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4" 
                              class="form-control @error('deskripsi') is-invalid @enderror" 
                              placeholder="Masukkan deskripsi HAKI...">{{ old('deskripsi', $haki->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 8. File Sertifikat (Opsional) -->
                <div class="col-12">
                    <label for="file_sertifikat" class="form-label fw-semibold">File Sertifikat (Opsional)</label>
                    <div class="p-3 bg-light rounded-3 mb-2 border">
                        <small class="text-secondary d-block mb-1 fw-semibold">
                            <i class="bi bi-info-circle me-1"></i> File saat ini: 
                            <span class="text-dark">{{ $haki->file_sertifikat ?? 'Belum ada file' }}</span>
                        </small>
                        <small class="text-muted d-block">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>
                    <input type="file" 
                           id="file_sertifikat" 
                           name="file_sertifikat" 
                           class="form-control @error('file_sertifikat') is-invalid @enderror" 
                           accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text text-muted">
                        Format yang diperbolehkan: PDF, JPG, JPEG, PNG. Maksimal 3 MB.
                    </div>
                    @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.haki.show', $haki) }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
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
