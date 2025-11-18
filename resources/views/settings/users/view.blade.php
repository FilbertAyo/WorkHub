<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="true">
                        <i class="fe fe-user me-1"></i> User Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="permissions-tab" data-toggle="tab" href="#permissions" role="tab"
                        aria-controls="permissions" aria-selected="false">
                        <i class="fe fe-shield me-1"></i> Permissions
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            @can('users management settings')
                <a href="{{ route('admin.edit', Hashids::encode($user->id)) }}" class="btn btn-sm btn-primary me-1">
                    <i class="fe fe-edit me-1"></i> Edit User
                </a>
            @endcan
            <a href="{{ route('admin.index') }}" class="btn btn-sm btn-light">
                <i class="fe fe-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="tab-content" id="myTabContent">
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-none border">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if ($user->file)
                                    <img src="{{ asset($user->file) }}" alt="{{ $user->name }}"
                                        class="rounded-circle shadow"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow"
                                        style="width: 150px; height: 150px; font-size: 60px; font-weight: 600;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-2">{{ $user->department?->name ?? 'No Department' }}</p>
                            
                            @if($user->roles->count() > 0)
                                <div class="mb-2">
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-primary badge-pill mb-1">
                                            <i class="fe fe-shield me-1"></i>
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mb-3">
                                <span
                                    class="badge badge-pill badge-{{ $user->status === 'active' ? 'success' : 'danger' }} px-3 py-2">
                                    <i
                                        class="fe fe-{{ $user->status === 'active' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>

                            <div class="d-grid gap-2">
                                @if ($user->status == 'active')
                                    @can('users management settings')
                                        @if ($user->email !== 'marscommunication.team@gmail.com')
                                            <form action="{{ route('admin.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fe fe-user-x me-1"></i> Deactivate User
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary w-100" disabled
                                                title="This account cannot be deactivated">
                                                <i class="fe fe-lock me-1"></i> Protected Account
                                            </button>
                                        @endif
                                    @endcan
                                @else
                                    @can('users management settings')
                                        <form action="{{ route('admin.activate', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to activate this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fe fe-user-check me-1"></i> Activate User
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-none border mt-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fe fe-activity me-2"></i>Quick Actions</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="list-group list-group-flush">
                                <a class="list-group-item list-group-item-action" href="mailto:{{ $user->email }}">
                                    <i class="fe fe-mail me-2 text-info"></i> Send Email
                                </a>
                                <a class="list-group-item list-group-item-action"
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                    target="_blank">
                                    <i class="fe fe-message-circle me-2 text-success"></i> WhatsApp
                                </a>
                                <a class="list-group-item list-group-item-action" href="tel:{{ $user->phone }}">
                                    <i class="fe fe-phone me-2 text-primary"></i> Call User
                                </a>
                                @can('users management settings')
                                    <button type="button" class="list-group-item list-group-item-action"
                                        data-toggle="modal" data-target="#addRole">
                                        <i class="fe fe-shield me-2 text-warning"></i> Manage Permissions
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details & Statistics -->
                <div class="col-lg-8">
                    <!-- User Information Card -->
                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fe fe-info me-2"></i>User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Full Name</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-user me-2 text-primary"></i>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Email Address</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-mail me-2 text-primary"></i>
                                        <strong>{{ $user->email }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Phone Number</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-phone me-2 text-primary"></i>
                                        <strong>{{ $user->phone }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Branch</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-git-branch me-2 text-primary"></i>
                                        <strong>{{ $user->branch?->name ?? 'No Branch' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Department</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-briefcase me-2 text-primary"></i>
                                        <strong>{{ $user->department?->name ?? 'No Department' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Account Status</label>
                                    <div class="d-flex align-items-center">
                                        <i
                                            class="fe fe-{{ $user->status === 'active' ? 'check-circle' : 'x-circle' }} me-2 text-{{ $user->status === 'active' ? 'success' : 'danger' }}"></i>
                                        <strong class="text-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($user->status) }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Role(s)</label>
                                    <div class="d-flex align-items-center flex-wrap">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-primary badge-pill me-2 mb-1">
                                                    <i class="fe fe-shield me-1"></i>
                                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">
                                                <i class="fe fe-shield-off me-2"></i>
                                                No role assigned
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Timeline -->
                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fe fe-clock me-2"></i>Account Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="avatar avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fe fe-user-plus"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1">Account Created</label>
                                            <div><strong>{{ $user->created_at->format('M d, Y') }}</strong></div>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="avatar avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fe fe-refresh-cw"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <label class="text-muted small mb-1">Last Updated</label>
                                            <div><strong>{{ $user->updated_at->format('M d, Y') }}</strong></div>
                                            <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                                @if ($user->email_verified_at)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="fe fe-check-circle"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <label class="text-muted small mb-1">Email Verified</label>
                                                <div><strong>{{ $user->email_verified_at->format('M d, Y') }}</strong>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $user->email_verified_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Current Permissions Summary -->
                    <div class="card shadow-none border">
                        <div
                            class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fe fe-shield me-2"></i>Current Permissions</h5>
                            <span class="badge badge-primary badge-pill">{{ $user->permissions->count() }}
                                Active</span>
                        </div>
                        <div class="card-body">
                            @if ($user->permissions->count() > 0)
                                <div class="row">
                                    @foreach ($user->permissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <span class="badge badge-light border px-3 py-2">
                                                <i class="fe fe-check text-success me-1"></i>
                                                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fe fe-shield text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mb-0 mt-2">No permissions assigned yet</p>
                                    @can('users management settings')
                                        <button type="button" class="btn btn-sm btn-primary mt-3" data-toggle="modal"
                                            data-target="#addRole">
                                            <i class="fe fe-plus me-1"></i> Assign Permissions
                                        </button>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Tab -->
        <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-none border">
                        <div
                            class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><i class="fe fe-shield me-2"></i>Manage User Permissions</h5>
                                <small class="text-muted">Assign or revoke permissions for this user</small>
                            </div>
                            @can('users management settings')
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addRole">
                                    <i class="fe fe-edit me-1"></i> Edit Permissions
                                </button>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-4 col-lg-3 mb-3">
                                        <div
                                            class="card h-100 {{ $user->hasPermissionTo($permission->name) ? 'border-success' : 'border-light' }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        @if ($user->hasPermissionTo($permission->name))
                                                            <i class="fe fe-check-circle text-success fs-4"></i>
                                                        @else
                                                            <i class="fe fe-circle text-muted fs-4"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <h6 class="mb-0">
                                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            @if ($user->hasPermissionTo($permission->name))
                                                                <span class="text-success">Granted</span>
                                                            @else
                                                                <span class="text-muted">Not Granted</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Permissions Modal -->
    <div class="modal fade" id="addRole" tabindex="-1" aria-labelledby="addRoleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="addRoleLabel">
                        <i class="fe fe-shield me-2 text-white"></i>Assign Permissions to {{ $user->name }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('assign.permissions', $user->id) }}" method="POST" id="permissionsForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fe fe-info me-2"></i>
                            <div>
                                <strong>Note:</strong> Select the permissions you want to grant to this user.
                                Unchecked permissions will be revoked.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Available Permissions ({{ $permissions->count() }})</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="selectAll()">
                                        Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="deselectAll()">
                                        Deselect All
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-6 mb-2">
                                        <div
                                            class="form-check p-3 border rounded {{ $user->hasPermissionTo($permission->name) ? 'bg-light' : '' }}">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="permissions[]" value="{{ $permission->name }}"
                                                id="perm_{{ $permission->id }}"
                                                {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="perm_{{ $permission->id }}">
                                                <strong>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fe fe-x me-1"></i> Cancel
                        </button>
                        @can('users management settings')
                            <button type="submit" class="btn btn-primary" id="assignBtn">
                                <i class="fe fe-check me-1"></i> Save Permissions
                            </button>
                        @else
                            <button type="button" class="btn btn-primary permission-alert">
                                <i class="fe fe-check me-1"></i> Save Permissions
                            </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .avatar {
            width: 40px;
            height: 40px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .form-check:hover {
            background-color: #f8f9fa !important;
        }
    </style>

    <script>
        function selectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Form submission handler
        const permissionsForm = document.getElementById('permissionsForm');
        const assignBtn = document.getElementById('assignBtn');

        if (permissionsForm && assignBtn) {
            permissionsForm.addEventListener('submit', function() {
                assignBtn.disabled = true;
                assignBtn.innerHTML = '<i class="fe fe-loader spinning me-1"></i> Saving...';
            });
        }
    </script>

</x-app-layout>
