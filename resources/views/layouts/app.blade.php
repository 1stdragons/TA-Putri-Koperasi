<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Koperasi Poltas Kreatif Mandiri</title>

    {{-- Font --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Bootstrap 5 (dimuat SEBELUM tema agar tema bisa override) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Design System Terpusat — override Bootstrap, wajib dimuat SETELAH Bootstrap --}}
    <link href="{{ asset('css/app-theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-bank2"></i></div>
            <div class="brand-text">
                <h6>Poltas Kreatif<br>Mandiri</h6>
                <small>Monitoring Anggota</small>
            </div>
        </div>

        <div class="sidebar-user">
            <img src="{{ Auth::user()->foto_url }}" alt="Foto Profil">
            <div>
                <div class="name">{{ Auth::user()->nama }}</div>
                <span class="role-badge">{{ Auth::user()->role_label }}</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @yield('sidebar-menu')

            <div class="nav-section-title">Akun</div>
            <form action="{{ route('logout') }}" method="POST" id="formLogout">
                @csrf
                <a href="#" class="nav-link" style="color:#f43f5e;"
                    onclick="event.preventDefault(); document.getElementById('formLogout').submit();">
                    <i class="bi bi-box-arrow-left" style="color:#f43f5e;"></i> Keluar
                </a>
            </form>
        </nav>
    </aside>

    {{-- ── TOPBAR ──────────────────────────────────────────────── --}}
    <header class="topbar">
        <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="page-subtitle">@yield('page-subtitle', '')</div>
        </div>

        <div class="topbar-right">
            <span class="text-muted small d-none d-md-flex align-items-center gap-1"
                style="font-size:.78rem; font-weight:500;">
                <i class="bi bi-calendar3 me-1"></i>
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </span>

            {{-- Notifikasi navbar hanya dimuat satu kali --}}
            @include('layouts._notifikasi-navbar')

            {{-- Profil --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown" style="border-radius:30px; padding:.3rem .75rem .3rem .4rem;">
                    <img src="{{ Auth::user()->foto_url }}" alt="Foto" class="avatar-sm">
                    <span class="d-none d-md-inline fw-semibold" style="font-size:.82rem;">
                        {{ Auth::user()->nama }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text" style="font-size:.76rem; color:var(--text-muted);">
                            {{ Auth::user()->role_label }}
                        </span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profil.index') }}">
                            <i class="bi bi-person me-2" style="color:var(--primary);"></i>
                            Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('formLogout').submit();">
                            <i class="bi bi-box-arrow-left me-2"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
    <main class="main-content page-fade-in">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('warning') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebarEl = document.getElementById('sidebar');
        const overlayEl = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebarEl.classList.toggle('show');
                overlayEl.classList.toggle('show');
            });
        }
        if (overlayEl) {
            overlayEl.addEventListener('click', () => {
                sidebarEl.classList.remove('show');
                overlayEl.classList.remove('show');
            });
        }

        document.querySelectorAll('.alert').forEach(el => {
            setTimeout(() => {
                const a = bootstrap.Alert.getOrCreateInstance(el);
                if (a) a.close();
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>

</html>
