<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Expensify</title>
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
        .register-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(64, 78, 59, .12), 0 1px 4px rgba(0,0,0,.06);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }

        /* ── Header ───────────────────────────────────────────────────────── */
        .register-header {
            background: #5a7052;
            padding: 2rem 2rem 1.75rem;
            text-align: center;
        }

        .register-logo {
            height: 48px;
            width: auto;
            display: block;
            margin: 0 auto .875rem;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }

        .register-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 .35rem;
            letter-spacing: -.01em;
        }

        .register-subtitle {
            font-size: .8rem;
            color: rgba(255,255,255,.6);
            margin: 0;
        }

        /* ── Body ─────────────────────────────────────────────────────────── */
        .register-body { padding: 1.75rem 2rem 2rem; }

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
        .alert-danger { background: #FEE2E2; color: #991B1B; }

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

        /* ── Submit button ────────────────────────────────────────────────── */
        .btn-register {
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

        .btn-register:hover:not(:disabled) {
            background: linear-gradient(135deg, #6B8659 0%, #5a7348 100%);
            box-shadow: 0 4px 14px rgba(107,134,89,.35);
            transform: translateY(-1px);
        }

        .btn-register:active:not(:disabled) { transform: translateY(0); box-shadow: none; }
        .btn-register:disabled { opacity: .7; cursor: not-allowed; }

        /* ── Divider ──────────────────────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid #E5E7EB;
            margin: 1.5rem 0 1.25rem;
        }

        /* ── Sign-in section ──────────────────────────────────────────────── */
        .signin-section { text-align: center; }
        .signin-section p { font-size: .85rem; color: #6B7280; margin-bottom: .75rem; }

        .btn-signin {
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
        .btn-signin:hover {
            background: #F3F4F6;
            color: #6B8659;
            border-color: #6B8659;
        }

        /* ── Responsive ───────────────────────────────────────────────────── */
        @media (max-width: 480px) {
            .register-body { padding: 1.5rem 1.25rem 1.75rem; }
            .register-header { padding: 1.5rem 1.25rem 1.25rem; }
            .register-title { font-size: 1.2rem; }
            .register-logo { height: 40px; }
            .btn-register { min-height: 48px; font-size: 1rem; }
            .pw-req-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="register-card">

    {{-- Header --}}
    <div class="register-header">
        <img src="{{ asset('images/Expensify.png') }}" alt="Expensify" class="register-logo">
        <h1 class="register-title">Create Your Account</h1>
        <p class="register-subtitle">Start tracking your finances today</p>
    </div>

    <div class="register-body">

        {{-- Flash / validation errors --}}
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
                    <strong>Registration failed.</strong>
                    @foreach ($errors->all() as $error)
                        <div class="mt-1">{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('register.store') }}" method="POST" novalidate id="registerForm">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Russel John Rosas"
                       value="{{ old('name') }}"
                       autocomplete="name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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
                           autocomplete="new-password"
                           required>
                    <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>

                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="pw-wrap">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           placeholder="Re-enter your password"
                           autocomplete="new-password" required>
                    <button type="button" class="pw-toggle" onclick="togglePw('password_confirmation', this)" tabindex="-1">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-register" id="registerBtn">
                <span class="btn-text">Create Account</span>
                <span class="btn-loader d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Creating account…
                </span>
            </button>
        </form>

        {{-- Sign in link --}}
        <p style="text-align:center; margin-top:1.25rem; font-size:.875rem; color:#6B7280;">
            Already have an account?
            <a href="{{ route('login') }}" style="color:#7B9669; font-weight:600; text-decoration:none;">Sign In</a>
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

    document.getElementById('registerForm').addEventListener('submit', function () {
        const btn    = document.getElementById('registerBtn');
        const text   = btn.querySelector('.btn-text');
        const loader = btn.querySelector('.btn-loader');
        text.classList.add('d-none');
        loader.classList.remove('d-none');
        btn.disabled = true;
    });
</script>
</body>
</html>
