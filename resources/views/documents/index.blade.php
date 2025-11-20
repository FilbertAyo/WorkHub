<x-app-layout>
    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-file-text me-1"></i> My Documents
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('documents.create') }}" class="btn mb-2 btn-primary btn-sm">
                <span class="btn-label">
                    <i class="fe fe-plus-circle"></i>
                </span>
                New Document
            </a>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0"><i class="fe fe-list me-2"></i>Documents List</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('documents.index') }}" class="row">
                                <div class="col-md-3 mb-2">
                                    <select name="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="weekly_plan" {{ request('type') === 'weekly_plan' ? 'selected' : '' }}>Weekly Plans</option>
                                        <option value="weekly_report" {{ request('type') === 'weekly_report' ? 'selected' : '' }}>Weekly Reports</option>
                                        <option value="monthly_report" {{ request('type') === 'monthly_report' ? 'selected' : '' }}>Monthly Reports</option>
                                        <option value="weekly_minutes" {{ request('type') === 'weekly_minutes' ? 'selected' : '' }}>Minutes</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <select name="period_id" class="form-control">
                                        <option value="">All Weeks</option>
                                        @foreach($periods as $period)
                                            <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                                Week {{ $period->week_number }} • {{ $period->date_range }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <select name="year" class="form-control">
                                        <option value="">All Years</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-6 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                                        <i class="fe fe-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('documents.index') }}" class="btn btn-light btn-sm">
                                        <i class="fe fe-x me-1"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1"><i class="fe fe-target me-1 text-info"></i>Plan Deadline</p>
                                        @if($deadlineStatuses)
                                            <h5 class="mb-1">{{ \Carbon\Carbon::parse($deadlineStatuses['plan_due'])->format('M d, Y') }}</h5>
                                            <span class="badge {{ $deadlineStatuses['plan_badge'] }}">{{ $deadlineStatuses['plan_text'] }}</span>
                                        @else
                                            <span class="text-muted">No current period</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('documents.index', ['type' => 'weekly_plan']) }}" class="btn btn-outline-info btn-sm">
                                        View Plans
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1"><i class="fe fe-activity me-1 text-primary"></i>Report Deadline</p>
                                        @if($deadlineStatuses)
                                            <h5 class="mb-1">{{ \Carbon\Carbon::parse($deadlineStatuses['report_due'])->format('M d, Y') }}</h5>
                                            <span class="badge {{ $deadlineStatuses['report_badge'] }}">{{ $deadlineStatuses['report_text'] }}</span>
                                        @else
                                            <span class="text-muted">No current period</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('documents.index', ['type' => 'weekly_report']) }}" class="btn btn-outline-primary btn-sm">
                                        View Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1"><i class="fe fe-calendar me-1 text-secondary"></i>Quick Period</p>
                                        @if($currentPeriod)
                                            <h5 class="mb-1">Week {{ $currentPeriod->week_number }}</h5>
                                            <small class="text-muted">{{ $currentPeriod->date_range }}</small>
                                        @else
                                            <span class="text-muted">No period</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('work-periods.index') }}" class="btn btn-outline-secondary btn-sm">
                                        Work Periods
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
</x-app-layout>

