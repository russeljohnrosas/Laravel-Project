@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<div class="profile-wrap">

    {{-- ══════════════════════════════════════════
         PROFILE HEADER
    ══════════════════════════════════════════ --}}
    <div class="profile-card profile-header-card text-center">

        {{-- Avatar --}}
        <div class="avatar-wrapper mx-auto" id="avatarWrapper">
            @if ($user->profile_picture)
                <img id="avatarImg"
                     src="{{ asset('storage/uploads/profiles/' . $user->profile_picture) }}"
                     alt="{{ $user->name }}"
                     class="avatar-img">
            @else
                <img id="avatarImg"
                     src="{{ asset('images/default.png') }}"
                     alt="{{ $user->name }}"
                     class="avatar-img">
            @endif

            <div class="avatar-overlay" onclick="document.getElementById('pictureInput').click()">
                <i class="ti ti-camera"></i>
            </div>
        </div>

        {{-- Hidden file inputs --}}
        <input type="file" id="pictureInput" name="picture"
               accept="image/jpeg,image/png,image/jpg,image/gif"
               class="d-none" onchange="handlePictureChange(this)">

        <form id="uploadPictureForm"
              action="{{ route('profile.picture') }}"
              method="POST"
              enctype="multipart/form-data"
              class="d-none">
            @csrf
            <input type="file" name="picture" id="uploadPictureInput">
        </form>

        {{-- Identity --}}
        <h4 class="profile-name mt-3">{{ $user->name }}</h4>
        <p class="profile-email">{{ $user->email }}</p>
        <p class="profile-since">
            <i class="ti ti-calendar me-1"></i>Member since {{ $user->created_at->format('F Y') }}
        </p>

        {{-- Change picture button --}}
        <button type="button"
                class="btn btn-outline-sage btn-sm mt-2"
                onclick="document.getElementById('pictureInput').click()">
            <i class="ti ti-camera me-1"></i>Change Picture
        </button>

    </div>


    {{-- ══════════════════════════════════════════
         PERSONAL INFORMATION
    ══════════════════════════════════════════ --}}
    <div class="profile-card">

        <div class="pcard-header">
            <div>
                <h6 class="pcard-title">Personal Information</h6>
                <p class="pcard-subtitle">Your account details</p>
            </div>
            <button class="btn btn-primary btn-sm px-3"
                    data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="ti ti-pencil me-1"></i>Edit Profile
            </button>
        </div>

        <div class="pcard-body">

            <div class="info-row">
                <div class="info-icon-wrap"><i class="ti ti-user"></i></div>
                <div class="info-content">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon-wrap"><i class="ti ti-mail"></i></div>
                <div class="info-content">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon-wrap"><i class="ti ti-phone"></i></div>
                <div class="info-content">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value {{ !$user->phone ? 'info-empty' : '' }}">
                        {{ $user->phone ?? 'Not added yet' }}
                    </div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon-wrap"><i class="ti ti-map-pin"></i></div>
                <div class="info-content">
                    <div class="info-label">Address</div>
                    <div class="info-value {{ !$user->address ? 'info-empty' : '' }}">
                        {{ $user->address ?? 'Not added yet' }}
                    </div>
                </div>
            </div>

            <div class="info-row info-row-last">
                <div class="info-icon-wrap"><i class="ti ti-cake"></i></div>
                <div class="info-content">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value {{ !$user->date_of_birth ? 'info-empty' : '' }}">
                        {{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'Not added yet' }}
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ══════════════════════════════════════════
         SECURITY
    ══════════════════════════════════════════ --}}
    <div class="profile-card">

        <div class="pcard-header">
            <div>
                <h6 class="pcard-title">Security</h6>
                <p class="pcard-subtitle">Manage your password and account access</p>
            </div>
        </div>

        <div class="pcard-body">
            <div class="info-row info-row-last">
                <div class="info-icon-wrap"><i class="ti ti-lock"></i></div>
                <div class="info-content">
                    <div class="info-label">Password</div>
                    <div class="info-value">••••••••••••</div>
                </div>
                <button class="btn btn-outline-sage btn-sm ms-auto flex-shrink-0"
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="ti ti-key me-1"></i>Change
                </button>
            </div>
        </div>

    </div>


    {{-- ══════════════════════════════════════════
         DANGER ZONE
    ══════════════════════════════════════════ --}}
    <div class="profile-card danger-card">

        <div class="pcard-header danger-header">
            <div>
                <h6 class="pcard-title danger-title">
                    <i class="ti ti-alert-triangle me-2"></i>Danger Zone
                </h6>
                <p class="pcard-subtitle danger-subtitle">
                    Permanently delete your account and all associated data.
                    This action cannot be undone.
                </p>
            </div>
        </div>

        <div class="pcard-body">
            <div class="info-row info-row-last">
                <div class="info-icon-wrap danger-icon-wrap"><i class="ti ti-user-x"></i></div>
                <div class="info-content">
                    <div class="info-label">Delete Account</div>
                    <div class="info-value" style="font-size:.8rem; color:#64748B;">
                        All your transactions, budgets, and profile data will be permanently removed.
                    </div>
                </div>
                <button class="btn btn-danger-soft btn-sm ms-auto flex-shrink-0"
                        data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="ti ti-trash me-1"></i>Delete
                </button>
            </div>
        </div>

    </div>

</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — EDIT PROFILE
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="editProfileLabel">
                    <i class="ti ti-user-edit me-2"></i>Edit Profile
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body-custom">
                    <div class="row g-3">

                        <div class="col-12">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   maxlength="100" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="phone" class="form-label">
                                Phone <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <input type="text" id="phone" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}"
                                   maxlength="20"
                                   placeholder="+63 900 000 0000">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="date_of_birth" class="form-label">
                                Date of Birth <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                   max="{{ now()->subDay()->format('Y-m-d') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">
                                Address <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea id="address" name="address"
                                      class="form-control @error('address') is-invalid @enderror"
                                      rows="2"
                                      maxlength="255"
                                      placeholder="Street, City, Province">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-device-floppy me-1"></i>Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — CHANGE PASSWORD
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom">
                <h5 class="modal-title-custom" id="changePasswordLabel">
                    <i class="ti ti-key me-2"></i>Change Password
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body-custom">
                    <div class="row g-3">

                        <div class="col-12">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-pw-wrap">
                                <input type="password" id="current_password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Enter current password" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('current_password', this)">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-pw-wrap">
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Minimum 6 characters" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('password', this)">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-pw-wrap">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control"
                                       placeholder="Re-enter new password" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('password_confirmation', this)">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="ti ti-device-floppy me-1"></i>Update Password
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL — DELETE ACCOUNT
══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content modal-custom">

            <div class="modal-header-custom modal-header-danger">
                <h5 class="modal-title-custom" id="deleteAccountLabel">
                    <i class="ti ti-alert-triangle me-2"></i>Delete Account
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body-custom">
                    <div class="delete-warning mb-4">
                        <p class="mb-2" style="font-size:.875rem; color:#374151;">
                            This will <strong>permanently delete</strong> your account including all transactions, budgets, and personal data.
                        </p>
                        <p class="mb-0" style="font-size:.875rem; color:#374151;">
                            Enter your password to confirm.
                        </p>
                    </div>

                    <div>
                        <label for="delete_password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-pw-wrap">
                            <input type="password" id="delete_password" name="delete_password"
                                   class="form-control @error('delete_password') is-invalid @enderror"
                                   placeholder="Enter your password" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('delete_password', this)">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        @error('delete_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="ti ti-trash me-1"></i>Delete My Account
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
    /* ── Layout ── */
    .profile-wrap {
        max-width: 680px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* ── Card base ── */
    .profile-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        overflow: hidden;
    }

    /* ── Profile header card ── */
    .profile-header-card {
        padding: 2.5rem 2rem 2rem;
    }

    /* ── Avatar ── */
    .avatar-wrapper {
        position: relative;
        width: 120px; height: 120px;
        border-radius: 50%;
        cursor: pointer;
    }

    .avatar-img {
        width: 120px; height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #7B9669;
        display: block;
    }

    .avatar-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(0,0,0,.45);
        color: #fff;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity .2s;
    }

    .avatar-wrapper:hover .avatar-overlay { opacity: 1; }

    .avatar-wrapper.uploading .avatar-overlay {
        opacity: 1;
        background: rgba(123,150,105,.65);
    }

    /* ── Header identity ── */
    .profile-name  { font-size: 1.2rem; font-weight: 700; color: #0F172A; margin-bottom: .2rem; }
    .profile-email { font-size: .875rem; color: #64748B; margin-bottom: .25rem; }
    .profile-since { font-size: .775rem; color: #94A3B8; margin-bottom: 0; }

    /* ── Outline sage button ── */
    .btn-outline-sage {
        border: 1.5px solid #7B9669;
        color: #7B9669;
        background: transparent;
        border-radius: 7px;
        font-size: .8rem;
        font-weight: 600;
        padding: .35rem .9rem;
        transition: background .15s, color .15s;
    }

    .btn-outline-sage:hover {
        background: #7B9669;
        color: #fff;
    }

    /* ── Card header / body ── */
    .pcard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        background: #F8FAFC;
    }

    .pcard-title    { font-size: .9rem; font-weight: 700; color: #0F172A; margin: 0; }
    .pcard-subtitle { font-size: .75rem; color: #94A3B8; margin: .15rem 0 0; }

    .pcard-body { padding: .5rem 0; }

    /* ── Info rows ── */
    .info-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: .85rem 1.5rem;
        border-bottom: 1px solid #F8FAFC;
        transition: background .12s;
    }

    .info-row:hover { background: #FAFBFC; }
    .info-row-last  { border-bottom: none; }

    .info-icon-wrap {
        width: 38px; height: 38px;
        border-radius: 9px;
        background: #EEF3EB;
        color: #7B9669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .875rem;
        flex-shrink: 0;
    }

    .info-content { flex: 1; min-width: 0; }

    .info-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94A3B8;
        margin-bottom: .2rem;
    }

    .info-value {
        font-size: .9rem;
        font-weight: 500;
        color: #0F172A;
        word-break: break-word;
    }

    .info-empty { color: #CBD5E1; font-style: italic; font-weight: 400; }

    /* ── Danger zone ── */
    .danger-card { border-color: #FECACA; }

    .danger-header { background: #FFF5F5; border-bottom-color: #FECACA; }

    .danger-title    { color: #DC2626; }
    .danger-subtitle { color: #9CA3AF; }

    .danger-icon-wrap { background: #FEE2E2; color: #DC2626; }

    .btn-danger-soft {
        background: #FEE2E2;
        color: #DC2626;
        border: 1.5px solid #FECACA;
        border-radius: 7px;
        font-size: .8rem;
        font-weight: 600;
        padding: .35rem .9rem;
        transition: background .15s, color .15s, border-color .15s;
    }

    .btn-danger-soft:hover {
        background: #DC2626;
        color: #fff;
        border-color: #DC2626;
    }

    /* ── Delete warning box ── */
    .delete-warning {
        background: #FFF5F5;
        border: 1px solid #FECACA;
        border-radius: 8px;
        padding: 1rem 1.1rem;
    }

    /* ── Modal ── */
    .modal-custom         { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
    .modal-header-custom  { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.5rem; background: #F8FAFC; border-bottom: 1px solid #E2E8F0; }
    .modal-header-danger  { background: #FFF5F5; border-bottom-color: #FECACA; }
    .modal-title-custom   { font-size: .95rem; font-weight: 700; color: #0F172A; margin: 0; }
    .modal-close          { background: none; border: none; color: #94A3B8; font-size: .95rem; cursor: pointer; padding: .2rem .4rem; border-radius: 6px; transition: background .15s, color .15s; }
    .modal-close:hover    { background: #E2E8F0; color: #374151; }
    .modal-body-custom    { padding: 1.5rem; }
    .modal-footer-custom  { display: flex; justify-content: flex-end; gap: .5rem; padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; background: #F8FAFC; }

    .modal-body-custom .form-label   { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
    .modal-body-custom .form-control { border: 1.5px solid #E5E7EB; border-radius: 8px; font-size: .875rem; padding: .5rem .75rem; }
    .modal-body-custom .form-control:focus { border-color: #7B9669; box-shadow: 0 0 0 3px rgba(123,150,105,.12); }

    /* ── Password toggle ── */
    .input-pw-wrap { position: relative; }

    .input-pw-wrap .form-control { padding-right: 2.5rem; }

    .pw-toggle {
        position: absolute;
        right: .6rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        font-size: .9rem;
        padding: 0;
        line-height: 1;
    }

    .pw-toggle:hover { color: #7B9669; }
</style>
@endpush


@push('scripts')
<script>
    // ── Modal auto-open on validation errors ─────────────────────────────
    @if ($errors->hasAny(['name', 'email', 'phone', 'address', 'date_of_birth']))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('editProfileModal')).show();
        });
    @elseif ($errors->hasAny(['current_password', 'password']) || session('open_password_modal'))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
        });
    @elseif ($errors->has('delete_password') || session('open_delete_modal'))
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
        });
    @endif

    // ── Picture preview & upload ─────────────────────────────────────────
    function handlePictureChange(input) {
        if (!input.files || !input.files[0]) return;

        const file   = input.files[0];
        const maxMb  = 2;

        if (file.size > maxMb * 1024 * 1024) {
            showToast(`Image must be under ${maxMb}MB.`, 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => { document.getElementById('avatarImg').src = e.target.result; };
        reader.readAsDataURL(file);

        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('uploadPictureInput').files = dt.files;

        document.getElementById('avatarWrapper').classList.add('uploading');
        document.getElementById('uploadPictureForm').submit();
    }

    // ── Password visibility toggle ───────────────────────────────────────
    function togglePw(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ti-eye', 'ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('ti-eye-off', 'ti-eye');
        }
    }
</script>
@endpush
