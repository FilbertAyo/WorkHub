<x-app-layout>
    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-home me-1"></i> My Dashboard
                    </a>
                </li>
            </ul>
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

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Pending Submissions</span>
                            <h3 class="mb-0 text-warning">{{ $stats['total_pending'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fe fe-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Submitted</span>
                            <h3 class="mb-0 text-success">{{ $stats['total_submitted'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fe fe-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Weekly Plans Pending</span>
                            <h3 class="mb-0 text-info">{{ $stats['weekly_plan_pending'] }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fe fe-calendar fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted d-block">Weekly Reports Pending</span>
                            <h3 class="mb-0 text-primary">{{ $stats['weekly_report_pending'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fe fe-file-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Submissions Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fe fe-clock me-2 text-warning"></i>
                            Pending Submissions
                            <span class="badge badge-warning ml-2">{{ $pendingDocuments->count() }}</span>
                        </h5>
                        <a href="{{ route('documents.create') }}" class="btn btn-sm btn-primary">
                            <i class="fe fe-plus-circle me-1"></i> Create New Document
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($pendingDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No.</th>
                                        <th>Document Type</th>
                                        <th>Title</th>
                                        <th>Created</th>
                                        <th>Last Updated</th>
                                        <th class="text-center" style="width: 150px;">Status</th>
                                        <th class="text-center" style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingDocuments as $document)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <i class="fe fe-file me-1"></i>
                                                    {{ $document->type_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $document->getDataField('title', 'Untitled') }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $document->created_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $document->updated_at->format('M d, Y') }}
                                                    <br>
                                                    <span class="text-muted">{{ $document->updated_at->diffForHumans() }}</span>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning">
                                                    <i class="fe fe-edit me-1"></i>Draft
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('documents.show', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}"
                                                        class="btn btn-info text-white" title="View">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('documents.edit', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}"
                                                        class="btn btn-primary text-white" title="Edit">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe fe-check-circle fs-1 text-success"></i>
                            <p class="text-muted mb-3 mt-3">No pending submissions</p>
                            <p class="text-muted small">All your documents have been submitted!</p>
                            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus-circle me-1"></i>Create New Document
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-none border">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fe fe-list me-2"></i>
                        Document History
                        <span class="badge badge-primary ml-2">{{ $allDocuments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($allDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No.</th>
                                        <th>Document Type</th>
                                        <th>Title</th>
                                        <th>Created</th>
                                        <th>Submitted</th>
                                        <th class="text-center" style="width: 120px;">Status</th>
                                        <th class="text-center" style="width: 100px;">Comments</th>
                                        <th class="text-center" style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allDocuments as $document)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <i class="fe fe-file me-1"></i>
                                                    {{ $document->type_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $document->getDataField('title', 'Untitled') }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $document->created_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($document->state === 'submitted')
                                                    <small class="text-muted">
                                                        {{ $document->updated_at->format('M d, Y') }}
                                                        <br>
                                                        <span class="text-muted">{{ $document->updated_at->diffForHumans() }}</span>
                                                    </small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($document->state === 'draft')
                                                    <span class="badge badge-warning">
                                                        <i class="fe fe-edit me-1"></i>Draft
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fe fe-check-circle me-1"></i>Submitted
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary badge-pill">
                                                    {{ $document->comments->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('documents.show', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}"
                                                    class="btn btn-info btn-sm text-white" title="View">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fe fe-file-text fs-1 text-muted"></i>
                            <p class="text-muted mb-3 mt-3">No documents yet</p>
                            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus-circle me-1"></i>Create Your First Document
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

