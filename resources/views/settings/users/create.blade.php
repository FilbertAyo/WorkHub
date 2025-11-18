<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-user-plus me-1"></i> Create New User
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.index') }}" class="btn btn-sm btn-light">
                <i class="fe fe-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('admin.store') }}" id="createUserForm">
                @csrf
                <div class="card shadow-none border">

                    <div class="card-body">
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-user"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            name="name"
                                            id="name"
                                            placeholder="Enter full name"
                                            value="{{ old('name') }}"
                                            required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-mail"></i>
                                        </span>
                                        <input type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email"
                                            id="email"
                                            placeholder="user@example.com"
                                            value="{{ old('email') }}"
                                            required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Login credentials will be sent to this email</small>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-phone"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            name="phone"
                                            id="phone"
                                            placeholder="07XXXXXXXX or 06XXXXXXXX"
                                            value="{{ old('phone') }}"
                                            maxlength="10"
                                            required>
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format: 07XXXXXXXX or 06XXXXXXXX</small>
                                </div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="department_id" class="form-label">
                                        Department <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-briefcase"></i>
                                        </span>
                                        <select class="form-control @error('department_id') is-invalid @enderror"
                                            name="department_id"
                                            id="department_id"
                                            required>
                                            <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>
                                                -- Select Department --
                                            </option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('department_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select the user's department</small>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="role" class="form-label">
                                        Role
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-shield"></i>
                                        </span>
                                        <select class="form-control @error('role') is-invalid @enderror"
                                            name="role"
                                            id="role">
                                            <option value="employee" {{ old('role', 'employee') == 'employee' ? 'selected' : '' }}>
                                                Employee (Default)
                                            </option>
                                            @foreach($roles as $role)
                                                @if($role->name !== 'employee')
                                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Default role is 'employee' if not specified</small>
                                </div>
                            </div>

                            <!-- Status Info Alert -->
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="fe fe-info me-2"></i>
                                    <div>
                                        <strong>Note:</strong> The user will be created with <strong>Active</strong> status
                                        and will receive an email with auto-generated login credentials.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                                <i class="fe fe-x me-1"></i> Cancel
                            </a>
                            @can('users management settings')
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fe fe-check-circle me-1"></i> Create User
                                </button>
                            @else
                                <button type="button" class="btn btn-primary permission-alert">
                                    <i class="fe fe-check-circle me-1"></i> Create User
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .spinning {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createUserForm');
            const submitBtn = document.getElementById('submitBtn');
            const phoneInput = document.getElementById('phone');

            // Phone number validation
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                e.target.value = value;
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fe fe-loader spinning me-1"></i> Creating User...';
                }
            });

            // Email validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('blur', function() {
                const email = this.value;
                if (email) {
                    // Basic email validation
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                }
            });
        });
    </script>

</x-app-layout>
