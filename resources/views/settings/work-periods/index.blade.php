<x-app-layout>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="periods-tab" data-toggle="tab" href="#periods" role="tab"
                        aria-controls="periods" aria-selected="true">
                        <i class="fe fe-calendar me-1"></i> Work Periods Management
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light" onclick="window.location.reload()" title="Refresh">
                <i class="fe fe-refresh-ccw"></i>
            </button>
            @if(Auth::user()->hasRole('admin'))
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createWorkPeriodModal">
                    <i class="fe fe-plus-circle me-1"></i> Add Work Period
                </button>
            @endif
        </div>
    </div>

    <!-- Current Period Info -->
    @if($currentPeriod)
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card border-primary shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">
                                <i class="fe fe-calendar me-2 text-primary"></i>
                                Current Period: Week {{ $currentPeriod->week_number }} of {{ $currentPeriod->year }}
                            </h5>
                            <p class="mb-0 text-muted">
                                {{ $currentPeriod->date_range }} |
                                Status: <span class="badge badge-{{ $currentPeriod->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($currentPeriod->status) }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <small class="text-muted">Plan Deadline:</small>
                                <strong class="d-block">{{ \Carbon\Carbon::parse($currentPeriod->plan_deadline)->format('M d, Y') }}</strong>
                            </div>
                            <div>
                                <small class="text-muted">Report Deadline:</small>
                                <strong class="d-block">{{ \Carbon\Carbon::parse($currentPeriod->report_deadline)->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-body">
                    <form method="GET" action="{{ route('work-periods.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Filter by Year</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Filter by Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fe fe-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('work-periods.index') }}" class="btn btn-light">
                                <i class="fe fe-x me-1"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Periods Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">All Work Periods</h5>
                </div>
                <div class="card-body">
                    @if($periods->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Week</th>
                                        <th>Date Range</th>
                                        <th>Plan Deadline</th>
                                        <th>Report Deadline</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periods as $period)
                                        <tr>
                                            <td><strong>{{ $period->year }}</strong></td>
                                            <td>Week {{ $period->week_number }}</td>
                                            <td>
                                                <small>{{ $period->date_range }}</small>
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($period->plan_deadline)->format('M d, Y') }}</small>
                                                @if($period->isPlanDeadlineToday())
                                                    <span class="badge badge-warning ml-1">Today</span>
                                                @elseif($period->isPlanDeadlinePassed())
                                                    <span class="badge badge-danger ml-1">Passed</span>
                                                @else
                                                    <span class="badge badge-info ml-1">{{ $period->getDaysUntilPlanDeadline() }} days</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($period->report_deadline)->format('M d, Y') }}</small>
                                                @if($period->isReportDeadlineToday())
                                                    <span class="badge badge-warning ml-1">Today</span>
                                                @elseif($period->isReportDeadlinePassed())
                                                    <span class="badge badge-danger ml-1">Passed</span>
                                                @else
                                                    <span class="badge badge-info ml-1">{{ $period->getDaysUntilReportDeadline() }} days</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($period->status === 'open')
                                                    <span class="badge badge-success">Open</span>
                                                @elseif($period->status === 'closed')
                                                    <span class="badge badge-secondary">Closed</span>
                                                @else
                                                    <span class="badge badge-dark">Archived</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $period->documents()->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('work-periods.show', Hashids::encode($period->id)) }}"
                                                       class="btn btn-sm btn-info" title="View">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->hasRole('admin'))
                                                        <a href="{{ route('work-periods.edit', Hashids::encode($period->id)) }}"
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fe fe-edit"></i>
                                                        </a>
                                                        @if($period->status === 'open')
                                                            <form action="{{ route('work-periods.close', Hashids::encode($period->id)) }}"
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to close this period?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-secondary" title="Close">
                                                                    <i class="fe fe-lock"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $periods->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe fe-calendar fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No work periods found.</p>
                            @if(Auth::user()->hasRole('admin'))
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createWorkPeriodModal">
                                    <i class="fe fe-plus-circle me-1"></i> Create First Work Period
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Work Period Modal -->
    @if(Auth::user()->hasRole('admin'))
    <div class="modal fade" id="createWorkPeriodModal" tabindex="-1" role="dialog" aria-labelledby="createWorkPeriodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('work-periods.store') }}" id="createPeriodForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createWorkPeriodModalLabel">
                            <i class="fe fe-calendar me-2"></i>Create Work Period
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fe fe-info me-2"></i>
                            <strong>Important:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Week will run from Monday to Sunday</li>
                                <li>Plan deadline: Friday (to submit plan for the NEXT week)</li>
                                <li>Report deadline: Saturday (to submit report for THIS week)</li>
                                <li>After creating this period, future weeks will be created automatically every Sunday</li>
                            </ul>
                        </div>

                        <div class="row">
                            <!-- Year -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="modal_year" class="form-label">
                                        Year <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-calendar"></i>
                                        </span>
                                        <input type="number"
                                            class="form-control @error('year') is-invalid @enderror"
                                            name="year"
                                            id="modal_year"
                                            min="2020"
                                            max="2100"
                                            value="{{ old('year', $currentYear ?? date('Y')) }}"
                                            required>
                                    </div>
                                    @error('year')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Week Number -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="modal_week_number" class="form-label">
                                        Week Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-hash"></i>
                                        </span>
                                        <input type="number"
                                            class="form-control @error('week_number') is-invalid @enderror"
                                            name="week_number"
                                            id="modal_week_number"
                                            min="1"
                                            max="53"
                                            value="{{ old('week_number', isset($lastPeriod) && $lastPeriod ? ($lastPeriod->year == date('Y') ? $lastPeriod->week_number + 1 : 1) : 1) }}"
                                            required>
                                    </div>
                                    @error('week_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Week number within the year (1-53)</small>
                                </div>
                            </div>

                            <!-- Week Start Date -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="modal_week_start_date" class="form-label">
                                        Week Start Date (Monday) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-calendar"></i>
                                        </span>
                                        <input type="date"
                                            class="form-control @error('week_start_date') is-invalid @enderror"
                                            name="week_start_date"
                                            id="modal_week_start_date"
                                            value="{{ old('week_start_date', isset($lastPeriod) && $lastPeriod ? \Carbon\Carbon::parse($lastPeriod->week_end_date)->addDay()->format('Y-m-d') : '') }}"
                                            required>
                                    </div>
                                    @error('week_start_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        The Monday of the week. End date and deadlines will be calculated automatically.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            <i class="fe fe-x me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i> Create Work Period
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Open modal if there are validation errors
        @if($errors->any() || session('error'))
            $(document).ready(function() {
                $('#createWorkPeriodModal').modal('show');
            });
        @endif

        // Close modal on successful submission (if no errors)
        @if(session('success'))
            $(document).ready(function() {
                $('#createWorkPeriodModal').modal('hide');
            });
        @endif
    </script>
    @endpush

</x-app-layout>

