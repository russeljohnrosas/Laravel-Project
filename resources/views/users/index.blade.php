@extends('layouts.app')

@section('title', 'Users Management')

@section('content')

<div class="page-header mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h2 class="mb-1">Users Management</h2>
        <p class="text-muted mb-0">{{ $users->count() }} total user{{ $users->count() !== 1 ? 's' : '' }}</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-success">
        <i class="ti ti-user-plus me-2"></i>Add User
    </a>
</div>

{{-- Users Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            @if($user->profile_pic)
                                <img src="{{ asset('uploads/' . $user->profile_pic) }}"
                                     alt="{{ $user->name }}"
                                     class="rounded-circle"
                                     style="width:36px;height:36px;object-fit:cover;border:2px solid #E5E7EB;">
                            @else
                                <div class="user-avatar" style="width:36px;height:36px;font-size:.8rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            {{-- Edit button --}}
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-sm btn-primary me-1"
                               title="Edit user">
                                <i class="ti ti-pencil"></i> Edit
                            </a>

                            {{-- Delete button (disabled for own account) --}}
                            @if($user->id !== session('user')['id'])
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete user">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                            @else
                            <button class="btn btn-sm btn-danger" disabled title="Cannot delete your own account">
                                <i class="ti ti-trash"></i> Delete
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ti ti-users" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.25;"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- ── Add User Modal ── --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">
                        <i class="ti ti-user-plus me-2 text-primary"></i>Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Full name" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Email address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-user-plus me-1"></i>Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ── Edit User Modal ── --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">
                        <i class="ti ti-pencil me-2 text-primary"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" id="editName" name="name" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ── Delete Confirmation Modal ── --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="deleteUserForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="mb-3">
                        <i class="ti ti-alert-triangle" style="font-size:3rem;color:#EF4444;opacity:.75;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Delete User?</h5>
                    <p class="text-muted mb-0">
                        You are about to delete <strong id="deleteUserName"></strong>.
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection




@push('scripts')
<script>
function openEditModal(id, name, email) {
    document.getElementById('editUserForm').action = '/users/' + id;
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function openDeleteModal(id, name) {
    document.getElementById('deleteUserForm').action = '/users/' + id;
    document.getElementById('deleteUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>
@endpush
