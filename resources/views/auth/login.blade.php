<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - HAKIKU</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(
                135deg,
                #0f172a 0%,
                #1e293b 50%,
                #0f172a 100%
            );
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            background: linear-gradient(
                135deg,
                #1e3a8a 0%,
                #3b82f6 100%
            );
            color: #ffffff;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            backdrop-filter: blur(4px);
        }

        .btn-primary-custom {
            background: linear-gradient(
                135deg,
                #2563eb 0%,
                #1d4ed8 100%
            );
            border: none;
            border-radius: 0.75rem;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(
                135deg,
                #1d4ed8 0%,
                #1e40af 100%
            );
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .input-group-text {
            border-radius: 0.75rem;
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .input-group .form-control {
            min-width: 0;
        }
    </style>
</head>

<body>

<div class="login-card">

    <!-- Header -->
    <div class="brand-header">
        <div class="brand-logo">
            <i class="bi bi-shield-check"></i>
        </div>

        <h4 class="fw-bold mb-1">HAKIKU</h4>

        <p class="small text-white-50 mb-0">
            Sistem Informasi Pencatatan Hak Kekayaan Intelektual
        </p>
    </div>

    <!-- Form Login -->
    <div class="p-4 p-sm-5">

        <!-- Notifikasi Berhasil -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4"
                 role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup"></button>
            </div>
        @endif

        <!-- Pesan Error -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4"
                 role="alert">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4">
            <h5 class="fw-bold mb-1">Masuk ke HAKIKU</h5>

            <p class="small text-secondary mb-0">
                Gunakan akun Portal Akademik untuk mahasiswa dan dosen,
                atau akun lokal untuk Admin.
            </p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- NIM/NIP/NIK atau Email Admin -->
            <div class="mb-3">

                <label for="username"
                       class="form-label small fw-semibold text-secondary">
                    NIM/NIP/NIK atau Email Admin
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-badge text-secondary"></i>
                    </span>

                    <input
                        type="text"
                        class="form-control @error('username') is-invalid @enderror"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan NIM/NIP/NIK atau email Admin"
                        autocomplete="username"
                        maxlength="255"
                        required
                        autofocus
                    >
                </div>

                @error('username')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">

                <label for="password"
                       class="form-label small fw-semibold text-secondary">
                    Password
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock text-secondary"></i>
                    </span>

                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        maxlength="255"
                        required
                    >
                </div>

                @error('password')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Ingat Saya -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >

                    <label class="form-check-label small text-secondary"
                           for="remember">
                        Ingat Saya
                    </label>
                </div>
            </div>

            <!-- Tombol Login -->
            <button type="submit"
                    class="btn btn-primary-custom text-white w-100 mb-3">

                <i class="bi bi-box-arrow-in-right me-1"></i>
                Masuk ke Sistem
            </button>

            <!-- Kembali ke Dashboard Publik -->
            <div class="text-center">
                <a href="{{ url('/') }}"
                   class="text-decoration-none small text-secondary">

                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Dashboard Publik
                </a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
