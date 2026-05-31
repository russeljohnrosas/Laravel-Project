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

@endsection
