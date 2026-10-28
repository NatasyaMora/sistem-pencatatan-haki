<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah HAKI - HAKIKU</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .navbar-custom {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
        }

        .form-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
            background: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: .875rem;
        }

        .form-control, .form-select {
            border-radius: .6rem;
            padding: .7rem .85rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
        }

        .readonly-box {
            background: #f1f5f9;
            color: #475569;
        }

        .section-title {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .required {
            color: #dc3545;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ url('/') }}">
            <i class="bi bi-shield-check fs-3"></i>
            <span>HAKIKU</span>
        </a>

        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="{{ url('/') }}">Dashboard Publik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-semibold" href="{{ route('user.haki.index') }}">HAKI Saya</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 py-2 py-lg-0">
                <span class="small fw-semibold text-secondary">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ $user->name }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="container my-4 my-md-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Pencatatan HAKI Baru</h4>
            <p class="text-secondary small mb-0">
                Catat data HAKI yang sudah Anda miliki beserta sertifikatnya.
            </p>
        </div>

        <a href="{{ route('user.haki.index') }}"
            class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3" role="alert">
            <div class="fw-bold mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Periksa kembali data yang kamu masukkan.
            </div>
            <ul class="mb-0 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-card p-3 p-md-5">
        <form action="{{ route('user.haki.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <section class="border-bottom pb-4 mb-4">
                <h6 class="section-title">
                    <i class="bi bi-person-vcard me-2"></i>Identitas Pemilik
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="pemilik_nama" class="form-label">Nama Pemilik</label>
                        <input type="text" id="pemilik_nama"
                            class="form-control readonly-box"
                            value="{{ $user->name }}" readonly>
                        <div class="form-text">Diambil otomatis dari akun yang sedang login.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="pemilik_identity" class="form-label">NIM / NIP / NIDN</label>
                        <input type="text" id="pemilik_identity"
                            class="form-control readonly-box"
                            value="{{ $user->identity_number ?? '-' }}" readonly>
                        <div class="form-text">Identitas akun tidak dapat diubah melalui form ini.</div>
                    </div>
                </div>
            </section>

            <section class="border-bottom pb-4 mb-4">
                <h6 class="section-title">
                    <i class="bi bi-journal-bookmark me-2"></i>Informasi Data HAKI
                </h6>

                <div class="mb-3">
                    <label for="judul_haki" class="form-label">
                        Judul HAKI <span class="required">*</span>
                    </label>
                    <input type="text" id="judul_haki" name="judul_haki"
                        class="form-control @error('judul_haki') is-invalid @enderror"
                        value="{{ old('judul_haki') }}" maxlength="255"
                        placeholder="Masukkan judul HAKI" required autofocus>
                    @error('judul_haki')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="jenis_haki_id" class="form-label">
                            Jenis HAKI <span class="required">*</span>
                        </label>
                        <select id="jenis_haki_id" name="jenis_haki_id"
                            class="form-select @error('jenis_haki_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis HAKI --</option>
                            @foreach ($jenisOptions as $jenis)
                                <option value="{{ $jenis->id }}"
                                    {{ (string) old('jenis_haki_id') === (string) $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_haki_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($jenisOptions->isEmpty())
                            <div class="form-text text-danger">
                                Belum ada jenis HAKI aktif. Hubungi Admin.
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="nomor_sertifikat" class="form-label">
                            Nomor Sertifikat <span class="required">*</span>
                        </label>
                        <input type="text" id="nomor_sertifikat" name="nomor_sertifikat"
                            class="form-control @error('nomor_sertifikat') is-invalid @enderror"
                            value="{{ old('nomor_sertifikat') }}" maxlength="255"
                            placeholder="Masukkan nomor sertifikat" required>
                        @error('nomor_sertifikat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="tanggal_terbit" class="form-label">
                            Tanggal Terbit <span class="required">*</span>
                        </label>
                        <input type="date" id="tanggal_terbit" name="tanggal_terbit"
                            class="form-control @error('tanggal_terbit') is-invalid @enderror"
                            value="{{ old('tanggal_terbit') }}" required>
                        @error('tanggal_terbit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_kadaluarsa" class="form-label">
                            Tanggal Kedaluwarsa <span class="required">*</span>
                        </label>
                        <input type="date" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa"
                            class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror"
                            value="{{ old('tanggal_kadaluarsa') }}" required>
                        @error('tanggal_kadaluarsa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Status aktif atau nonaktif ditentukan berdasarkan tanggal ini.
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="faculty_id" class="form-label">
                            Fakultas <span class="required">*</span>
                        </label>
                        <select id="faculty_id" name="faculty_id"
                            class="form-select @error('faculty_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}"
                                    {{ (string) old('faculty_id') === (string) $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($faculties->isEmpty())
                            <div class="form-text text-danger">
                                Belum ada fakultas aktif. Hubungi Admin.
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="study_program_id" class="form-label">
                            Program Studi <span class="required">*</span>
                        </label>
                        <select id="study_program_id" name="study_program_id"
                            class="form-select @error('study_program_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($studyPrograms as $program)
                                <option value="{{ $program->id }}"
                                    data-faculty="{{ $program->faculty_id }}"
                                    {{ (string) old('study_program_id') === (string) $program->id ? 'selected' : '' }}>
                                    {{ $program->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('study_program_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($studyPrograms->isEmpty())
                            <div class="form-text text-danger">
                                Belum ada program studi aktif. Hubungi Admin.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-1">
                    <label for="deskripsi" class="form-label">
                        Deskripsi / Abstrak <span class="text-secondary fw-normal">(Opsional)</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Tuliskan gambaran singkat tentang HAKI ini">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            <section class="pb-2">
                <h6 class="section-title">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Berkas Sertifikat
                </h6>

                <div class="mb-3">
                    <label for="file_sertifikat" class="form-label">
                        Unggah Sertifikat HAKI <span class="required">*</span>
                    </label>
                    <input type="file" id="file_sertifikat" name="file_sertifikat"
                        class="form-control @error('file_sertifikat') is-invalid @enderror"
                        accept=".pdf,.jpg,.jpeg,.png" required>
                    @error('file_sertifikat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Format PDF, JPG, JPEG, atau PNG. Ukuran maksimal 3 MB.
                    </div>
                </div>
            </section>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-4 mt-3 border-top">
                <a href="{{ route('user.haki.index') }}"
                    class="btn btn-light border rounded-pill px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-save me-1"></i> Simpan Pencatatan HAKI
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center text-secondary small">
        &copy; {{ date('Y') }} HAKIKU — Portal Pencatatan HAKI Kampus.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const facultySelect = document.getElementById('faculty_id');
    const programSelect = document.getElementById('study_program_id');
    const tanggalTerbit = document.getElementById('tanggal_terbit');
    const tanggalKadaluarsa = document.getElementById('tanggal_kadaluarsa');

    function filterStudyPrograms() {
        const facultyId = facultySelect.value;

        Array.from(programSelect.options).forEach(option => {
            if (!option.dataset.faculty) return;

            const matches = option.dataset.faculty === facultyId;
            option.hidden = !matches;
            option.disabled = !matches;
        });

        const selected = programSelect.selectedOptions[0];

        if (selected && selected.dataset.faculty &&
            selected.dataset.faculty !== facultyId) {
            programSelect.value = '';
        }
    }

    function updateMinimumExpiryDate() {
        tanggalKadaluarsa.min = tanggalTerbit.value || '';

        if (tanggalTerbit.value && tanggalKadaluarsa.value &&
            tanggalKadaluarsa.value < tanggalTerbit.value) {
            tanggalKadaluarsa.setCustomValidity(
                'Tanggal kedaluwarsa harus sama dengan atau setelah tanggal terbit.'
            );
        } else {
            tanggalKadaluarsa.setCustomValidity('');
        }
    }

    facultySelect.addEventListener('change', filterStudyPrograms);
    tanggalTerbit.addEventListener('change', updateMinimumExpiryDate);
    tanggalKadaluarsa.addEventListener('change', updateMinimumExpiryDate);

    filterStudyPrograms();
    updateMinimumExpiryDate();
</script>

</body>
</html>
