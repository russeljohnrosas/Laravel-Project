<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Expensify</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Expensify.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #EEF2EC 0%, #F9FAFB 60%, #E8EEE5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', 'Segoe UI', system-ui, sans-serif;
            color: #1F2937;
            padding: 1.25rem;
        }

        /* ── Card ─────────────────────────────────────────────────────────── */
        .login-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(64, 78, 59, .12), 0 1px 4px rgba(0,0,0,.06);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        /* ── Header ───────────────────────────────────────────────────────── */
        .login-header {
            background: #5a7052;
            padding: 2rem 2rem 1.75rem;
            text-align: center;
        }

        .login-logo {
            height: 48px;
            width: auto;
            display: block;
            margin: 0 auto .875rem;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 .35rem;
            letter-spacing: -.01em;
        }

        .login-subtitle {
            font-size: .8rem;
            color: rgba(255,255,255,.6);
            margin: 0;
        }

        /* ── Body ─────────────────────────────────────────────────────────── */
        .login-body { padding: 1.75rem 2rem 2rem; }

        /* ── Alerts ───────────────────────────────────────────────────────── */
        .alert {
            border-radius: 8px;
            font-size: .85rem;
            border: none;
            padding: .75rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            margin-bottom: 1.25rem;
        }
        .alert i { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-danger  { background: #FEE2E2; color: #991B1B; }
        .alert .btn-close { margin-left: auto; filter: brightness(0) saturate(100%) invert(13%) sepia(72%) saturate(500%) hue-rotate(332deg); }

        /* ── Form ─────────────────────────────────────────────────────────── */
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .375rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #D1D5DB;
            padding: .65rem .9rem;
            font-size: .9rem;
            color: #1F2937;
            background: #fff;
            width: 100%;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: #7B9669;
            box-shadow: 0 0 0 4px rgba(123,150,105,.12);
            outline: none;
        }

        .form-control::placeholder { color: #9CA3AF; }

        .form-control.is-invalid { border-color: #DC2626; }
        .form-control.is-invalid:focus { box-shadow: 0 0 0 4px rgba(220,38,38,.12); }

        .invalid-feedback { font-size: .775rem; color: #DC2626; margin-top: .3rem; }

        /* ── Password wrapper ─────────────────────────────────────────────── */
        .pw-wrap { position: relative; }
        .pw-wrap .form-control { padding-right: 2.75rem; }
        .pw-toggle {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            transition: color .15s;
        }
        .pw-toggle:hover { color: #7B9669; }

        /* ── Checkbox ─────────────────────────────────────────────────────── */
        .form-check-input { width: 16px; height: 16px; cursor: pointer; border: 1.5px solid #D1D5DB; }
        .form-check-input:checked { background-color: #7B9669; border-color: #7B9669; }
        .form-check-input:focus { box-shadow: 0 0 0 3px rgba(123,150,105,.15); }
        .form-check-label { font-size: .85rem; color: #6B7280; cursor: pointer; }

        /* ── Submit button ────────────────────────────────────────────────── */
        .btn-login {
            background: linear-gradient(135deg, #7B9669 0%, #6B8659 100%);
            border: none;
            border-radius: 8px;
            padding: .7rem;
            font-weight: 600;
            font-size: .95rem;
            color: #fff;
            width: 100%;
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            min-height: 44px;
        }

        .btn-login:hover:not(:disabled) {
            background: linear-gradient(135deg, #6B8659 0%, #5a7348 100%);
            box-shadow: 0 4px 14px rgba(107,134,89,.35);
            transform: translateY(-1px);
        }

        .btn-login:active:not(:disabled) { transform: translateY(0); box-shadow: none; }

        .btn-login:disabled { opacity: .7; cursor: not-allowed; }

        /* ── Divider ──────────────────────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid #E5E7EB;
            margin: 1.5rem 0 1.25rem;
        }

        /* ── Signup section ───────────────────────────────────────────────── */
        .signup-section { text-align: center; }
        .signup-section p { font-size: .85rem; color: #6B7280; margin-bottom: .75rem; }

        .btn-signup {
            display: block;
            width: 100%;
            padding: .6rem;
            border: 2px solid #7B9669;
            border-radius: 8px;
            background: transparent;
            color: #7B9669;
            font-weight: 600;
            font-size: .9rem;
            text-align: center;
            text-decoration: none;
            transition: all .2s ease;
        }
        .btn-signup:hover {
            background: #F3F4F6;
            color: #6B8659;
            border-color: #6B8659;
        }

        /* ── Responsive ───────────────────────────────────────────────────── */
        @media (max-width: 480px) {
            .login-body { padding: 1.5rem 1.25rem 1.75rem; }
            .login-header { padding: 1.5rem 1.25rem 1.25rem; }
            .login-title { font-size: 1.3rem; }
            .login-logo { height: 40px; }
            .btn-login { min-height: 48px; font-size: 1rem; }
        }
    </style>
</head>
<body>

<div class="login-card">

    {{-- Header --}}
    <div class="login-header">
        <img src="{{ asset('images/Expensify.png') }}" alt="Expensify" class="login-logo">
        <h1 class="login-title">Expensify</h1>
        <p class="login-subtitle">Manage your expenses, track your budget</p>
    </div>

    <div class="login-body">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <i class="ti ti-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                <i class="ti ti-alert-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="ti ti-alert-circle"></i>
                <div>
                    <strong>Login failed.</strong>
                    @foreach ($errors->all() as $error)
                        <div class="mt-1">{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login.store') }}" method="POST" novalidate id="loginForm">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="russel@gmail.com"
                       value="{{ old('email') }}"
                       autocomplete="email" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="pw-wrap">
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password"
                           autocomplete="current-password" required>
                    <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember"
                           value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">Sign In</span>
                <span class="btn-loader d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Signing in…
                </span>
            </button>
        </form>

        {{-- Sign up link --}}
        <p style="text-align:center; margin-top:1.25rem; font-size:.875rem; color:#6B7280;">
            Don't have an account?
            <a href="{{ route('register') }}" style="color:#7B9669; font-weight:600; text-decoration:none;">Create Account</a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ti-eye', 'ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('ti-eye-off', 'ti-eye');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn    = document.getElementById('loginBtn');
        const text   = btn.querySelector('.btn-text');
        const loader = btn.querySelector('.btn-loader');
        text.classList.add('d-none');
        loader.classList.remove('d-none');
        btn.disabled = true;
    });
</script>
</body>
</html>
