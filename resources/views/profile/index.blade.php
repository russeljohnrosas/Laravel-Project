@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="row g-4">

    {{-- LEFT: Profile picture + info --}}
    <div class="col-12 col-md-4">
        <div class="app-card p-4 text-center h-100">

            {{-- Avatar --}}
            <div class="mb-3">
                @if(session('user')['profile_pic'] ?? null)
                    <img src="{{ asset('uploads/' . session('user')['profile_pic']) }}"
                         alt="{{ $user->name }}"
                         class="rounded-circle border"
                         style="width:160px;height:160px;object-fit:cover;border:3px solid #BAC8B1 !important;">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                         style="width:160px;height:160px;background:#EEF3EB;font-size:3.5rem;font-weight:700;color:#5a7052;border:3px solid #BAC8B1;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h5 class="fw-bold mb-1" style="color:#1F2937;">{{ $user->name }}</h5>
            <p class="text-muted mb-1" style="font-size:.875rem;">{{ $user->email }}</p>
            <p class="text-muted" style="font-size:.8rem;">
                <i class="ti ti-calendar me-1"></i>Member since {{ $user->created_at->format('F j, Y') }}
            </p>

            @if($user->is_admin)
                <span class="badge mt-1" style="background:#EEF3EB;color:#5a7052;font-size:.75rem;padding:.35rem .75rem;">
                    <i class="ti ti-shield-check me-1"></i>Administrator
                </span>
            @endif

        </div>
    </div>

    {{-- RIGHT: Edit form --}}
    <div class="col-12 col-md-8">
        <div class="app-card p-4">
            <div class="app-card-title mb-4">Edit Profile</div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Profile picture upload --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Profile Picture
                        <span class="text-muted fw-normal">(jpg, png, gif — max 2 MB)</span>
                    </label>
                    <input type="file"
                           name="profile"
                           id="profileInput"
                           class="form-control @error('profile') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif"
                           onchange="previewPic(this)">
                    @error('profile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="previewWrap" class="mt-2 d-none">
                        <img id="picPreview" src="" alt="Preview"
                             class="rounded-circle border"
                             style="width:64px;height:64px;object-fit:cover;border-color:#BAC8B1 !important;">
                        <span class="ms-2 text-muted" style="font-size:.8rem;">New picture preview</span>
                    </div>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Your full name"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}"
                           placeholder="you@example.com"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Save Changes
                </button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="app-card p-4 mt-4">
            <div class="app-card-title mb-4">Change Password</div>

            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                @if(session('open_password_modal'))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                        <i class="ti ti-alert-circle me-1"></i>
                        @error('current_password') {{ $message }} @else Please correct the errors below. @enderror
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Current Password</label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               placeholder="Current password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">New Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min 6 characters" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Repeat new password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="ti ti-lock me-1"></i>Update Password
                </button>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="app-card p-4 mt-4" style="border-left:3px solid #EF4444;">
            <div class="app-card-title mb-1" style="color:#DC2626;">Danger Zone</div>
            <p class="text-muted mb-3" style="font-size:.85rem;">
                Permanently delete your account and all your data. This cannot be undone.
            </p>

            <button class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="collapse" data-bs-target="#deleteAccountForm">
                <i class="ti ti-trash me-1"></i>Delete My Account
            </button>

            <div class="collapse mt-3" id="deleteAccountForm">
                <form action="{{ route('profile.destroy') }}" method="POST"
                      onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account.')">
                    @csrf
                    @method('DELETE')

                    @error('delete_password')
                        <div class="alert alert-danger py-2 mb-2" style="font-size:.85rem;">{{ $message }}</div>
                    @enderror

                    <div class="d-flex gap-2 align-items-center">
                        <input type="password" name="delete_password"
                               class="form-control"
                               placeholder="Enter your password to confirm"
                               style="max-width:280px;">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="ti ti-alert-triangle me-1"></i>Yes, delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function previewPic(input) {
    const wrap    = document.getElementById('previewWrap');
    const preview = document.getElementById('picPreview');
    if (!input.files || !input.files[0]) { wrap.classList.add('d-none'); return; }
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        wrap.classList.remove('d-none');
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
