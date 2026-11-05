<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Publik - HAKIKU</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --navy: #102e67;
            --blue: #1765dc;
            --light-blue: #eaf2ff;
            --text: #142f65;
            --muted: #64748b;
            --border: #dce8fc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: linear-gradient(135deg, #f4f8ff, #ffffff 55%, #eef4ff);
            min-height: 100vh;
        }

        /* =========================
           NAVBAR
        ========================= */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid #e5edfa;
            backdrop-filter: blur(12px);
        }

        .brand-logo {
            color: var(--blue);
            font-size: 1.65rem;
        }

        .brand-name {
            color: var(--blue);
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .navbar-divider {
            height: 30px;
            width: 1px;
            background: #b8c9e8;
            margin: 0 18px;
        }

        .navbar-label {
            color: var(--navy);
            font-weight: 700;
        }

        .btn-login {
            background: var(--blue);
            color: #fff;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 700;
            border: none;
            transition: 0.2s;
        }

        .btn-login:hover {
            background: #104eae;
            color: #fff;
            transform: translateY(-1px);
        }

        /* =========================
           HERO / BANNER KAMPUS
        ========================= */
        .hero-section {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: linear-gradient(110deg, #102652 0%, #1d4288 65%, #1556bb 100%);
            color: white;
            padding: 22px 38px;
            min-height: 170px;
            display: flex;
            align-items: center;
            box-shadow: 0 8px 22px rgba(27, 73, 145, 0.14);
        }

        .hero-section::before {
            content: "";
            position: absolute;
            right: -50px;
            top: -180px;
            width: 430px;
            height: 430px;
            border-radius: 50%;
            border: 45px solid rgba(83, 151, 255, 0.22);
        }

        .hero-section::after {
            content: "";
            position: absolute;
            right: -140px;
            bottom: -220px;
            width: 440px;
            height: 300px;
            border-radius: 50%;
            background: rgba(79, 148, 255, 0.3);
            transform: rotate(-25deg);
        }

        .campus-brand {
            position: relative;
            z-index: 2;
            width: 190px;
            min-width: 190px;
            height: 125px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-right: 25px;
            border-right: 2px solid rgba(255, 255, 255, 0.65);
        }

        .campus-brand img {
            display: block;
            width: auto;
            height: 125px;
            max-width: 100%;
            object-fit: contain;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding-left: 45px;
        }

        .hero-content h1 {
            font-size: clamp(1.4rem, 3vw, 2rem);
            font-weight: 800;
            margin-bottom: 8px;
        }

        .hero-content p {
            color: #d8e8ff;
            font-size: 1rem;
            line-height: 1.65;
            margin: 0;
            max-width: 800px;
        }

        /* =========================
           STATISTICS
        ========================= */
        .stat-card {
            height: 100%;
            position: relative;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 19px;
            box-shadow: 0 4px 14px rgba(35, 84, 155, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(35, 84, 155, 0.1);
        }

        .stat-card::after {
            content: "";
            position: absolute;
            width: 130px;
            height: 75px;
            right: -20px;
            bottom: -35px;
            border-radius: 60% 40% 0 0;
            background: linear-gradient(135deg, rgba(88, 155, 255, 0.04), rgba(88, 155, 255, 0.22));
            transform: rotate(-15deg);
        }

        .stat-icon {
            width: 74px;
            height: 74px;
            min-width: 74px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 2rem;
        }

        .icon-blue {
            background: #e5efff;
            color: #2674e9;
        }

        .icon-green {
            background: #e4f6f3;
            color: #2aa994;
        }

        .stat-label {
            color: #173a7c;
            font-size: 0.98rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .stat-value {
            color: #153675;
            font-size: 2.05rem;
            font-weight: 800;
            line-height: 1.2;
        }

        /* =========================
           CHART CARDS
        ========================= */
        .chart-card {
            height: 100%;
            background: white;
            border: 1px solid var(--border);
            border-radius: 19px;
            padding: 23px;
            box-shadow: 0 4px 15px rgba(35, 84, 155, 0.055);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 13px;
            font-size: 1.14rem;
            font-weight: 800;
            color: #123875;
            margin-bottom: 22px;
        }

        .section-title i {
            font-size: 1.8rem;
            color: #174a9e;
        }

        .faculty-chart-layout {
            display: grid;
            grid-template-columns: minmax(180px, 0.85fr) minmax(230px, 1.15fr);
            align-items: center;
            gap: 20px;
        }

        .donut-container {
            width: 100%;
            max-width: 245px;
            height: 235px;
            margin: auto;
            position: relative;
        }

        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .donut-center strong {
            font-size: 1.9rem;
            font-weight: 800;
            color: #143877;
        }

        .donut-center span {
            font-size: 0.85rem;
            color: #183b79;
        }

        .legend-list {
            display: flex;
            flex-direction: column;
            gap: 11px;
        }

        .legend-item {
            display: grid;
            grid-template-columns: 12px minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 9px;
            font-size: 0.78rem;
            color: #17386f;
        }

        .legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        .legend-name {
            overflow-wrap: anywhere;
        }

        .legend-count,
        .legend-percent {
            font-weight: 700;
            white-space: nowrap;
        }

        .program-chart-container {
            height: 290px;
            position: relative;
        }

        .empty-chart {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #8192ae;
            text-align: center;
        }

        .empty-chart i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        /* =========================
           INFORMATION CARDS
        ========================= */
        .info-card {
            position: relative;
            min-height: 178px;
            height: 100%;
            padding: 20px 22px;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: white;
            box-shadow: 0 4px 14px rgba(35, 84, 155, 0.06);
        }

        .info-card-content {
            position: relative;
            z-index: 2;
            max-width: 90%;
        }

        .info-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: #1352b6;
            font-size: 1.12rem;
            font-weight: 800;
        }

        .info-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e7f0ff;
            font-size: 1.4rem;
            color: #2674e9;
        }

        .info-card p {
            color: #294878;
            font-size: 0.8rem;
            line-height: 1.65;
            margin-bottom: 0;
        }

        .info-decoration {
            position: absolute;
            right: 15px;
            bottom: 12px;
            font-size: 5rem;
            color: rgba(48, 119, 221, 0.12);
            transform: rotate(-8deg);
        }

        .info-card-dark {
            background: linear-gradient(120deg, #112b60, #2456b8);
            border: none;
            color: white;
        }

        .info-card-dark .info-heading {
            color: white;
        }

        .info-card-dark .info-icon {
            background: rgba(255, 255, 255, 0.13);
            color: #ffc344;
        }

        .info-card-dark p {
            color: #e1edff;
        }

        .info-card-dark .info-decoration {
            color: rgba(255, 255, 255, 0.1);
        }

        /* =========================
           FOOTER
        ========================= */
        footer {
            color: #7889a6;
            font-size: 0.8rem;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 767px) {
            .hero-section {
                padding: 24px 20px;
                text-align: center;
                flex-direction: column;
            }

            .campus-brand {
                width: 100%;
                min-width: 0;
                height: 115px;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.5);
                padding: 0 0 15px;
                margin-bottom: 18px;
            }

            .campus-brand img {
                height: 105px;
            }

            .hero-content {
                padding: 0;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                min-width: 60px;
                font-size: 1.6rem;
            }

            .stat-value {
                font-size: 1.7rem;
            }

            .faculty-chart-layout {
                grid-template-columns: 1fr;
            }

            .legend-item {
                font-size: 0.75rem;
            }

            .info-card-content {
                max-width: 100%;
            }

            .info-decoration {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- =========================
         NAVBAR
    ========================= -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container py-2">

            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="bi bi-shield-check brand-logo"></i>
                <span class="brand-name">HAKIKU</span>
            </a>

            <div class="navbar-divider d-none d-md-block"></div>

            <span class="navbar-label d-none d-md-inline">
                Dashboard Publik
            </span>

            <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <span class="navbar-label d-md-none mt-3 mb-2">
                    Dashboard Publik
                </span>

                <div class="ms-auto d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mt-3 mt-lg-0">

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ url('/admin/dashboard') }}" class="btn btn-login">
                                <i class="bi bi-speedometer2 me-1"></i>
                                Dashboard Admin
                            </a>
                        @else
                            <a href="{{ url('/my-haki') }}" class="btn btn-login">
                                <i class="bi bi-folder-check me-1"></i>
                                HAKI Saya
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4 w-100">
                                <i class="bi bi-box-arrow-right me-1"></i>
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login
                        </a>
                    @endauth

                </div>
            </div>
        </div>
    </nav>

    <!-- =========================
         HERO / BANNER KAMPUS
    ========================= -->
    <header class="container mt-4 mb-4">

        <div class="hero-section">

            <div class="campus-brand">
                <img
                    src="{{ asset('images/logo-uinssc.png') }}"
                    alt="Logo Universitas Islam Negeri Siber Syekh Nurjati Cirebon"
                >
            </div>

            <div class="hero-content">

                <h1>
                    Sistem Informasi Pencatatan HAKI Kampus
                </h1>

                <p>
                    Portal resmi informasi publikasi Hak Kekayaan Intelektual
                    yang dimiliki oleh Civitas Akademika
                    UIN Siber Syekh Nurjati Cirebon.
                </p>

            </div>

        </div>

    </header>

    <main class="container mb-5">

        <!-- =========================
             STATISTIK DASHBOARD
        ========================= -->
        <section class="row g-3 mb-4">

            <!-- TOTAL DATA HAKI -->
            <div class="col-12 col-sm-6 col-xl-3">

                <div class="stat-card">

                    <div class="d-flex align-items-center gap-3">

                        <div class="stat-icon icon-blue">
                            <i class="bi bi-journal-text"></i>
                        </div>

                        <div>
                            <div class="stat-label">
                                Total Data HAKI
                            </div>

                            <div class="stat-value">
                                {{ number_format($totalHakiAktif, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- JENIS HAKI -->
            <div class="col-12 col-sm-6 col-xl-3">

                <div class="stat-card">

                    <div class="d-flex align-items-center gap-3">

                        <div class="stat-icon icon-green">
                            <i class="bi bi-stack"></i>
                        </div>

                        <div>
                            <div class="stat-label">
                                Jenis HAKI
                            </div>

                            <div class="stat-value">
                                {{ number_format($totalJenisHaki, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- FAKULTAS -->
            <div class="col-12 col-sm-6 col-xl-3">

                <div class="stat-card">

                    <div class="d-flex align-items-center gap-3">

                        <div class="stat-icon icon-blue">
                            <i class="bi bi-bank"></i>
                        </div>

                        <div>
                            <div class="stat-label">
                                Fakultas
                            </div>

                            <div class="stat-value">
                                {{ number_format($totalFakultas, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- JURUSAN -->
            <div class="col-12 col-sm-6 col-xl-3">

                <div class="stat-card">

                    <div class="d-flex align-items-center gap-3">

                        <div class="stat-icon icon-blue">
                            <i class="bi bi-mortarboard"></i>
                        </div>

                        <div>
                            <div class="stat-label">
                                Jurusan
                            </div>

                            <div class="stat-value">
                                {{ number_format($totalJurusan, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- =========================
             GRAFIK STATISTIK
        ========================= -->
        <section class="row g-3 mb-4">

            <!-- STATISTIK PER FAKULTAS -->
            <div class="col-12 col-xl-6">

                <div class="chart-card">

                    <h5 class="section-title">
                        <i class="bi bi-bar-chart-fill"></i>
                        Statistik HAKI per Fakultas
                    </h5>

                    @if ($facultyStats->isNotEmpty())

                        <div class="faculty-chart-layout">

                            <div class="donut-container">

                                <canvas id="facultyChart"></canvas>

                                <div class="donut-center">
                                    <strong>
                                        {{ number_format($totalHakiAktif, 0, ',', '.') }}
                                    </strong>

                                    <span>
                                        Total HAKI
                                    </span>
                                </div>

                            </div>

                            <div class="legend-list">

                                @php
                                    $chartColors = [
                                        '#234b95',
                                        '#2d91df',
                                        '#2eb5bc',
                                        '#3678ee',
                                        '#69b5ef',
                                        '#7e9fe9',
                                        '#a5c1f7',
                                        '#50a9d2',
                                    ];
                                @endphp

                                @foreach ($facultyStats as $index => $faculty)

                                    @php
                                        $percentage = $totalHakiAktif > 0
                                            ? ($faculty->total_haki / $totalHakiAktif) * 100
                                            : 0;
                                    @endphp

                                    <div class="legend-item">

                                        <span
                                            class="legend-dot"
                                            style="background-color: {{ $chartColors[$index % count($chartColors)] }};">
                                        </span>

                                        <span class="legend-name">
                                            {{ $faculty->nama }}
                                        </span>

                                        <span class="legend-count">
                                            {{ $faculty->total_haki }}
                                        </span>

                                        <span class="legend-percent">
                                            {{ number_format($percentage, 1, ',', '.') }}%
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @else

                        <div class="empty-chart">

                            <i class="bi bi-pie-chart"></i>

                            <strong>
                                Belum ada data statistik fakultas.
                            </strong>

                            <small>
                                Data akan muncul setelah HAKI dipublikasikan.
                            </small>

                        </div>

                    @endif

                </div>

            </div>

            <!-- STATISTIK PER JURUSAN -->
            <div class="col-12 col-xl-6">

                <div class="chart-card">

                    <h5 class="section-title">
                        <i class="bi bi-mortarboard-fill"></i>
                        Statistik HAKI per Jurusan
                    </h5>

                    @if ($studyProgramStats->isNotEmpty())

                        <div class="program-chart-container">
                            <canvas id="programChart"></canvas>
                        </div>

                    @else

                        <div class="empty-chart">

                            <i class="bi bi-bar-chart"></i>

                            <strong>
                                Belum ada data statistik jurusan.
                            </strong>

                            <small>
                                Data akan muncul setelah HAKI dipublikasikan.
                            </small>

                        </div>

                    @endif

                </div>

            </div>

        </section>

        <!-- =========================
             INFORMASI HAKIKU
        ========================= -->
        <section class="row g-3">

            <!-- APA ITU HAKI -->
            <div class="col-12 col-md-6 col-xl-4">

                <div class="info-card">

                    <div class="info-card-content">

                        <div class="info-heading">

                            <span class="info-icon">
                                <i class="bi bi-lightbulb-fill"></i>
                            </span>

                            Apa itu HAKI?

                        </div>

                        <p>
                            Hak Kekayaan Intelektual (HAKI) adalah hak eksklusif
                            yang diberikan kepada pencipta atau penemu atas
                            karya mereka, seperti karya tulis, teknologi,
                            merek, dan lainnya.
                        </p>

                    </div>

                    <i class="bi bi-file-earmark-medical info-decoration"></i>

                </div>

            </div>

            <!-- TENTANG HAKIKU -->
            <div class="col-12 col-md-6 col-xl-4">

                <div class="info-card">

                    <div class="info-card-content">

                        <div class="info-heading">

                            <span class="info-icon">
                                <i class="bi bi-star-fill"></i>
                            </span>

                            Tentang HAKIKU

                        </div>

                        <p>
                            HAKIKU merupakan sistem pencatatan HAKI
                            di lingkungan UIN Siber Syekh Nurjati Cirebon
                            untuk mendukung perlindungan karya civitas akademika.
                        </p>

                    </div>

                    <i class="bi bi-journal-bookmark-fill info-decoration"></i>

                </div>

            </div>

            <!-- DUKUNG INOVASI -->
            <div class="col-12 col-xl-4">

                <div class="info-card info-card-dark">

                    <div class="info-card-content">

                        <div class="info-heading">

                            <span class="info-icon">
                                <i class="bi bi-trophy-fill"></i>
                            </span>

                            Dukung Inovasi, Wujudkan Kampus Unggul

                        </div>

                        <p>
                            Dengan pencatatan HAKI yang tertib,
                            kita bersama menjaga karya, menumbuhkan inovasi,
                            dan memperkuat reputasi UINSSC.
                        </p>

                    </div>

                    <i class="bi bi-award-fill info-decoration"></i>

                </div>

            </div>

        </section>

    </main>

    <!-- =========================
         FOOTER
    ========================= -->
    <footer class="mt-auto py-4 text-center">

        <div class="container">

            &copy; {{ date('Y') }} HAKIKU —
            Sistem Informasi Pencatatan Hak Kekayaan Intelektual Kampus.

        </div>

    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- =========================
         CHART CONFIGURATION
    ========================= -->
    <script>
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#234477';

        const facultyLabels = @json($facultyStats->pluck('nama')->values());
        const facultyValues = @json($facultyStats->pluck('total_haki')->values());

        const programLabels = @json($studyProgramStats->pluck('nama')->values());
        const programValues = @json($studyProgramStats->pluck('total_haki')->values());

        const chartColors = [
            '#234b95',
            '#2d91df',
            '#2eb5bc',
            '#3678ee',
            '#69b5ef',
            '#7e9fe9',
            '#a5c1f7',
            '#50a9d2'
        ];

        // Diagram donat fakultas
        const facultyCanvas = document.getElementById('facultyChart');

        if (facultyCanvas && facultyValues.length > 0) {

            new Chart(facultyCanvas, {

                type: 'doughnut',

                data: {
                    labels: facultyLabels,

                    datasets: [{
                        data: facultyValues,
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 7
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '58%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' HAKI';
                                }
                            }
                        }
                    }

                }

            });

        }

        // Diagram batang jurusan
        const programCanvas = document.getElementById('programChart');

        if (programCanvas && programValues.length > 0) {

            new Chart(programCanvas, {

                type: 'bar',

                data: {
                    labels: programLabels,

                    datasets: [{
                        label: 'Jumlah HAKI',
                        data: programValues,
                        backgroundColor: '#245bb3',
                        hoverBackgroundColor: '#347ce5',
                        borderRadius: 5,
                        barThickness: 20
                    }]
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' HAKI';
                                }
                            }
                        }
                    },

                    scales: {

                        x: {
                            beginAtZero: true,

                            ticks: {
                                precision: 0
                            },

                            grid: {
                                color: '#e6edf8'
                            }
                        },

                        y: {
                            grid: {
                                display: false
                            },

                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }

                    }

                }

            });

        }
    </script>

</body>
</html>
