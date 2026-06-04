<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Expensify</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Expensify.png') }}">

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <!-- Layout and theme -->
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

<div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

<aside id="sidebar" class="sidebar">

    <div class="sidebar-brand">
        <img src="{{ asset('images/Expensify.png') }}" alt="Expensify" class="sidebar-logo">
        <span class="sidebar-brand-name">Expensify</span>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section">Main</span>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-home nav-icon"></i>
            <span>Dashboard</span>
        </a>

        <span class="nav-section">Finance</span>
        <a href="{{ route('transactions.index') }}"
           class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
            <i class="ti ti-list nav-icon"></i>
            <span>Transactions</span>
        </a>
        <a href="{{ route('budgets.index') }}"
           class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
            <i class="ti ti-target nav-icon"></i>
            <span>Budgets</span>
        </a>
        <a href="{{ route('accounts.index') }}"
           class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
            <i class="ti ti-wallet nav-icon"></i>
            <span>Accounts</span>
        </a>

        <span class="nav-section">Account</span>
        <a href="{{ route('profile.index') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="ti ti-user nav-icon"></i>
            <span>Profile</span>
        </a>

        @if(session('user')['is_admin'] ?? false)
        <span class="nav-section">Management</span>
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="ti ti-users nav-icon"></i>
            <span>Users</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-logout">
                <i class="ti ti-logout nav-icon"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<div class="main-wrapper">

    <header class="topbar">
        <button class="topbar-toggle d-lg-none" onclick="toggleSidebar()" aria-label="Toggle navigation">
            <i class="ti ti-menu-2"></i>
        </button>

        <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>

        <div class="topbar-right">
            <div class="dropdown">
                <button class="user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(session('user')['profile_pic'] ?? null)
                        <img src="{{ asset('uploads/' . session('user')['profile_pic']) }}"
                             alt="{{ session('user')['name'] ?? '' }}"
                             class="user-avatar rounded-circle"
                             style="object-fit:cover;width:32px;height:32px;padding:0;">
                    @else
                        <div class="user-avatar">
                            {{ strtoupper(substr(session('user')['name'] ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="user-info d-none d-sm-block">
                        <div class="user-name">{{ session('user')['name'] ?? 'User' }}</div>
                        <div class="user-email">{{ session('user')['email'] ?? '' }}</div>
                    </div>
                    <i class="ti ti-chevron-down dropdown-caret d-none d-sm-inline" style="font-size:.75rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                    <li>
                        <div class="dropdown-header-info">
                            @if(session('user')['profile_pic'] ?? null)
                                <img src="{{ asset('uploads/' . session('user')['profile_pic']) }}"
                                     alt="{{ session('user')['name'] ?? '' }}"
                                     class="rounded-circle"
                                     style="width:40px;height:40px;object-fit:cover;border:2px solid #BAC8B1;">
                            @else
                                <div class="user-avatar-lg">
                                    {{ strtoupper(substr(session('user')['name'] ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;font-size:.875rem;color:#1F2937;">{{ session('user')['name'] ?? '' }}</div>
                                <div style="font-size:.75rem;color:#9CA3AF;">{{ session('user')['email'] ?? '' }}</div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="ti ti-user me-2" style="color:#9CA3AF;"></i> Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-logout me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="content-area">

        {{-- Flash messages (Bootstrap alerts shown before toast JS fires) --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <span>&copy; {{ date('Y') }} Expensify. All rights reserved.</span>
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
</script>

@if (session('success'))
    <script>showToast('{{ addslashes(session("success")) }}', 'success')</script>
@endif
@if (session('error'))
    <script>showToast('{{ addslashes(session("error")) }}', 'error')</script>
@endif
@if (session('warning'))
    <script>showToast('{{ addslashes(session("warning")) }}', 'warning')</script>
@endif

@stack('scripts')

</body>
</html>
