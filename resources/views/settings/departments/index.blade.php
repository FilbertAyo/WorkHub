<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="departments-tab" data-toggle="tab" href="#departments" role="tab"
                        aria-controls="departments" aria-selected="true">
                        <i class="fe fe-briefcase me-1"></i> Departments Management
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light" onclick="location.reload()" title="Refresh">
                <i class="fe fe-refresh-ccw"></i>
            </button>
            @can('update other settings')
                <button type="button" class="btn btn-primary btn-sm" id="newDepartmentBtn">
                    <i class="fe fe-plus-circle me-1"></i> New Department
                </button>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Total Departments</span>
                            <h3 class="mb-0">{{ $departments->count() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fe fe-briefcase fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Active Departments</span>
                            <h3 class="mb-0 text-success">{{ $departments->where('status', 'active')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fe fe-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card borde shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Total Users</span>
                            <h3 class="mb-0">{{ $departments->sum('users_count') }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fe fe-users fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Form Card -->
    <div class="row my-2" id="formSection" style="display: none;">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white" id="formTitle">
                            <i class="fe fe-plus-circle me-2"></i>Add New Department
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" id="cancelFormBtn">
                            <i class="fe fe-x"></i> Cancel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('department.store') }}" id="departmentForm">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="department_id" id="departmentId">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="departmentName" class="form-label">
                                    Department Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fe fe-briefcase"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        name="name"
                                        id="departmentName"
                                        placeholder="Enter department name"
                                        required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="departmentStatus" class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fe fe-activity"></i>
                                    </span>
                                    <select name="status"
                                        id="departmentStatus"
                                        class="form-control @error('status') is-invalid @enderror"
                                        required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                           
                            @can('update other settings')
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fe fe-save me-1"></i> Save Department
                                </button>
                            @else
                                <button type="button" class="btn btn-primary permission-alert">
                                    <i class="fe fe-save me-1"></i> Save Department
                                </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="row my-2">
        @include('elements.spinner')
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h5 class="mb-0"><i class="fe fe-list me-2"></i>Departments List</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fe fe-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput"
                                    placeholder="Search departments by name...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <select class="form-control form-control-sm" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-sm btn-secondary w-100" onclick="resetFilters()">
                                <i class="fe fe-rotate-ccw me-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="departmentsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No.</th>
                                    <th>Department Name</th>
                                    <th class="text-center">Users</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($departments as $index => $department)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary text-white rounded me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fe fe-briefcase"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $department->name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light border">
                                                <i class="fe fe-users me-1"></i>
                                                {{ $department->users_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-pill {{ $department->status == 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                <i class="fe fe-{{ $department->status == 'active' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ ucfirst($department->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('department.show', Hashids::encode($department->id)) }}"
                                                    class="btn btn-info text-white" title="View Details">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                @can('update other settings')
                                                    <button type="button" class="btn btn-primary edit-btn"
                                                        data-id="{{ Hashids::encode($department->id) }}"
                                                        data-name="{{ $department->name }}"
                                                        data-status="{{ $department->status }}"
                                                        title="Edit Department">
                                                        <i class="fe fe-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger delete-btn"
                                                        data-id="{{ Hashids::encode($department->id) }}"
                                                        data-name="{{ $department->name }}"
                                                        title="Delete Department">
                                                        <i class="fe fe-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fe fe-briefcase fs-1 text-muted"></i>
                                            <p class="text-muted mb-0">No departments found</p>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">
                        <i class="fe fe-alert-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete the department <strong id="deleteDepartmentName"></strong>?</p>
                    <p class="text-danger small mb-0 mt-2">
                        <i class="fe fe-alert-circle me-1"></i>
                        This action cannot be undone. All associated users will also be affected.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fe fe-x me-1"></i> Cancel
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fe fe-trash me-1"></i> Delete Department
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }
        .avatar-sm {
            width: 32px;
            height: 32px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formSection = document.getElementById('formSection');
            const departmentForm = document.getElementById('departmentForm');
            const formTitle = document.getElementById('formTitle');
            const newDepartmentBtn = document.getElementById('newDepartmentBtn');
            const cancelFormBtn = document.getElementById('cancelFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const table = document.getElementById('departmentsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Show form for new department
            if (newDepartmentBtn) {
                newDepartmentBtn.addEventListener('click', function() {
                    resetForm();
                    formSection.style.display = 'block';
                    formTitle.innerHTML = '<i class="fe fe-plus-circle me-2"></i>Add New Department';
                    departmentForm.scrollIntoView({ behavior: 'smooth' });
                });
            }

            // Cancel buttons
            [cancelFormBtn, cancelBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        formSection.style.display = 'none';
                        resetForm();
                    });
                }
            });

            // Edit department
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    departmentForm.action = `/department/${id}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('departmentId').value = id;
                    document.getElementById('departmentName').value = this.dataset.name;
                    document.getElementById('departmentStatus').value = this.dataset.status;

                    formTitle.innerHTML = '<i class="fe fe-edit me-2"></i>Edit Department';
                    formSection.style.display = 'block';
                    departmentForm.scrollIntoView({ behavior: 'smooth' });
                });
            });

            // Delete department
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    document.getElementById('deleteDepartmentName').textContent = name;
                    document.getElementById('deleteForm').action = `/department/${id}`;
                    $('#deleteModal').modal('show');
                });
            });

            // Filter table
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length === 1) return; // Skip empty state row

                    const name = cells[1].textContent.toLowerCase();
                    const status = cells[3].textContent.toLowerCase();

                    const matchesSearch = name.includes(searchTerm);
                    const matchesStatus = !statusValue || status.includes(statusValue);

                    if (matchesSearch && matchesStatus) {
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

            function resetForm() {
                departmentForm.reset();
                departmentForm.action = '{{ route('department.store') }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('departmentId').value = '';
            }

            searchInput.addEventListener('keyup', filterTable);
            statusFilter.addEventListener('change', filterTable);

            // Show form if there are validation errors
            @if($errors->any())
                formSection.style.display = 'block';
            @endif
        });

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';

            const event = new Event('change');
            document.getElementById('statusFilter').dispatchEvent(event);
        }
    </script>

</x-app-layout>

