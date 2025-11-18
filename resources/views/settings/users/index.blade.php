<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab"
                        aria-controls="users" aria-selected="true">
                        <i class="fe fe-users me-1"></i> Users Management
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light" onclick="reloadPage()" title="Refresh">
                <i class="fe fe-refresh-ccw"></i>
            </button>
            @can('users management settings')
                <a href="{{ route('admin.create') }}" class="btn btn-primary btn-sm">
                    <i class="fe fe-plus-circle me-1"></i> New User
                </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Total Users</span>
                            <h3 class="mb-0">{{ $user->count() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fe fe-users fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Active Users</span>
                            <h3 class="mb-0 text-success">{{ $user->where('status', 'active')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fe fe-user-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Inactive Users</span>
                            <h3 class="mb-0 text-danger">{{ $user->where('status', 'inactive')->count() }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fe fe-user-x fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-2">
        @include('elements.spinner')
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0"><i class="fe fe-list me-2"></i>Users List</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fe fe-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput"
                                    placeholder="Search users by name, email, phone...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2">
                            <select class="form-control form-control-sm" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-control form-control-sm" id="branchFilter">
                                <option value="">All Branches</option>
                                @foreach ($user->pluck('branch')->unique('id')->sortBy('name') as $branch)
                                    @if($branch)
                                        <option value="{{ $branch->name }}">{{ $branch->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-control form-control-sm" id="departmentFilter">
                                <option value="">All Departments</option>
                                @foreach ($user->pluck('department')->unique('id')->sortBy('name') as $department)
                                    @if($department)
                                        <option value="{{ $department->name }}">{{ $department->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-sm btn-secondary w-100" onclick="resetFilters()">
                                <i class="fe fe-rotate-ccw me-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="usersTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user as $index => $u)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">

                                                <div>
                                                    <strong>{{ $u->name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $u->email }}
                                        </td>
                                        <td>
                                            {{ $u->phone }}
                                        </td>

                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $u->department->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($u->roles->count() > 0)
                                                @foreach($u->roles as $role)
                                                    <span class="badge badge-primary badge-pill mb-1">
                                                        <i class="fe fe-shield me-1"></i>
                                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">
                                                    <i class="fe fe-shield-off me-1"></i>
                                                    No role
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill {{ $u->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                                <i class="fe fe-{{ $u->status === 'active' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ ucfirst($u->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.show', Hashids::encode($u->id)) }}"
                                                    class="btn btn-info text-white" title="View Details">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                @can('users management settings')
                                                    <a href="{{ route('admin.edit', Hashids::encode($u->id)) }}"
                                                        class="btn btn-primary text-white" title="Edit User">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fe fe-users fs-1 text-muted"></i>
                                            <p class="text-muted mb-0">No users found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const branchFilter = document.getElementById('branchFilter');
            const departmentFilter = document.getElementById('departmentFilter');
            const table = document.getElementById('usersTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();
                const branchValue = branchFilter.value.toLowerCase();
                const departmentValue = departmentFilter.value.toLowerCase();

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length === 1) return; // Skip empty state row

                    const name = cells[1].textContent.toLowerCase();
                    const email = cells[2].textContent.toLowerCase();
                    const phone = cells[3].textContent.toLowerCase();
                    const branch = cells[4].textContent.toLowerCase();
                    const department = cells[5].textContent.toLowerCase();
                    const role = cells[6].textContent.toLowerCase();
                    const status = cells[7].textContent.toLowerCase();

                    const matchesSearch = name.includes(searchTerm) ||
                                        email.includes(searchTerm) ||
                                        phone.includes(searchTerm);
                    const matchesStatus = !statusValue || status.includes(statusValue);
                    const matchesBranch = !branchValue || branch.includes(branchValue);
                    const matchesDepartment = !departmentValue || department.includes(departmentValue);

                    if (matchesSearch && matchesStatus && matchesBranch && matchesDepartment) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateRowNumbers();
            }

            function updateRowNumbers() {
                let visibleIndex = 1;
                rows.forEach(row => {
                    if (row.style.display !== 'none' && row.querySelectorAll('td').length > 1) {
                        row.querySelector('td:first-child').textContent = visibleIndex++;
                    }
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            statusFilter.addEventListener('change', filterTable);
            branchFilter.addEventListener('change', filterTable);
            departmentFilter.addEventListener('change', filterTable);
        });

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('branchFilter').value = '';
            document.getElementById('departmentFilter').value = '';

            const event = new Event('change');
            document.getElementById('statusFilter').dispatchEvent(event);
        }

        function reloadPage() {
            location.reload();
        }
    </script>

    <style>
        .avatar {
            width: 32px;
            height: 32px;
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>

</x-app-layout>
