@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-semibold" style="color:#1F2937;">My Profile</h4>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
        <i class="ti ti-pencil me-1"></i>Edit Profile
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body p-4">

        {{-- Profile picture --}}
        <div class="text-center mb-4">
            @if($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                     alt="{{ $user->name }}"
                     class="rounded-circle border"
                     style="width:100px; height:100px; object-fit:cover; border-color:#E5E7EB !important;">
            @else
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                     style="width:100px; height:100px; background:#EEF3EB; font-size:2.5rem; font-weight:700; color:#5a7052;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- Info rows --}}
        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted fw-semibold" style="width:140px;">Name</td>
                <td class="fw-semibold" style="color:#1F2937;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="text-muted fw-semibold">Email</td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="text-muted fw-semibold">Member since</td>
                <td>{{ $user->created_at->format('F j, Y') }}</td>
            </tr>
        </table>

    </div>
</div>

{{-- Change Password --}}
<div class="card border-0 shadow-sm mt-4" style="max-width:520px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3" style="color:#1F2937;">Change Password</h6>

        @if(session('open_password_modal'))
            <div class="alert alert-danger py-2" style="font-size:.85rem;">
                <i class="ti ti-alert-circle me-1"></i>
                @error('current_password') {{ $message }} @else Please correct the errors below. @enderror
            </div>
        @endif

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;">Current Password</label>
                <input type="password" name="current_password"
                       class="form-control @error('current_password') is-invalid @enderror"
                       placeholder="Enter current password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;">New Password</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="At least 6 characters" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:.85rem;">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat new password" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="ti ti-lock me-1"></i>Update Password
            </button>
        </form>
    </div>
</div>

{{-- Danger Zone --}}
<div class="card border-0 shadow-sm mt-4" style="max-width:520px;border-top:2px solid #FEE2E2 !important;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-1" style="color:#DC2626;">Danger Zone</h6>
        <p class="text-muted mb-3" style="font-size:.85rem;">Permanently delete your account and all data. This cannot be undone.</p>

        <button class="btn btn-outline-danger btn-sm"
                data-bs-toggle="collapse" data-bs-target="#deleteAccountForm">
            <i class="ti ti-trash me-1"></i>Delete My Account
        </button>

        <div class="collapse mt-3" id="deleteAccountForm">
            <form action="{{ route('profile.destroy') }}" method="POST"
                  onsubmit="return confirm('Are you sure? This will permanently delete your account.')">
                @csrf
                @method('DELETE')
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">Confirm with your password</label>
                    <input type="password" name="delete_password"
                           class="form-control @error('delete_password') is-invalid @enderror"
                           placeholder="Enter your password to confirm">
                    @error('delete_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="ti ti-alert-triangle me-1"></i>Yes, delete my account
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
