@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('profile.index') }}" class="btn btn-light btn-sm">
        <i class="ti ti-arrow-left me-1"></i>Back
    </a>
    <h4 class="mb-0 fw-semibold" style="color:#1F2937;">Edit Profile</h4>
</div>

<div class="card border-0 shadow-sm" style="max-width:520px;">
    <div class="card-body p-4">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Current picture preview --}}
            <div class="text-center mb-4">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                         alt="{{ $user->name }}"
                         class="rounded-circle border mb-2"
                         id="picturePreview"
                         style="width:90px; height:90px; object-fit:cover; border-color:#E5E7EB !important;">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                         id="pictureInitial"
                         style="width:90px; height:90px; background:#EEF3EB; font-size:2rem; font-weight:700; color:#5a7052;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <img src="" alt="" class="rounded-circle border d-none mb-2"
                         id="picturePreview"
                         style="width:90px; height:90px; object-fit:cover; border-color:#E5E7EB !important;">
                @endif
            </div>

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
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
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
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

            {{-- Profile picture upload --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Profile Picture
                    <span class="text-muted fw-normal">(optional — jpg, png, gif, max 2 MB)</span>
                </label>
                <input type="file"
                       name="picture"
                       id="pictureInput"
                       class="form-control @error('picture') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/gif"
                       onchange="previewImage(this)">
                @error('picture')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Update Profile
                </button>
                <a href="{{ route('profile.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('picturePreview');
        const initial = document.getElementById('pictureInitial');
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        if (initial) initial.classList.add('d-none');
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
