<x-app-layout>
    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-clipboard me-1"></i> Create Weekly Minutes
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('minutes.index') }}" class="btn btn-sm btn-light">
                <i class="fe fe-arrow-left me-1"></i> Back to Minutes
            </a>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('minutes.store') }}" id="createMinutesForm">
                @csrf
                <div class="card shadow-none border">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fe fe-clipboard me-2"></i>Weekly Minutes Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        Title (Optional)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-type"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control @error('title') is-invalid @enderror"
                                            name="title"
                                            id="title"
                                            placeholder="e.g., Weekly Minutes - Week of Nov 11, 2024"
                                            value="{{ old('title') }}">
                                    </div>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank to auto-generate title with current date</small>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="content" class="form-label">
                                        Minutes Content <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                        name="content"
                                        id="content"
                                        rows="20"
                                        placeholder="Enter the weekly minutes content here. Include meeting date, attendees, discussion points, decisions made, action items, etc..."
                                        required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 10 characters required. Include all meeting details, decisions, and action items.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('minutes.index') }}" class="btn btn-secondary">
                                <i class="fe fe-x me-1"></i> Cancel
                            </a>
                            <div class="btn-group">
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary" id="saveDraftBtn">
                                    <i class="fe fe-save me-1"></i> Save Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fe fe-send me-1"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createMinutesForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const clickedButton = e.submitter;
                const action = clickedButton?.value || 'draft';
                
                if (action === 'submit') {
                    if (!confirm('Are you sure you want to submit these minutes? Once submitted, they cannot be edited.')) {
                        e.preventDefault();
                        return false;
                    }
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fe fe-loader spinning me-1"></i> Submitting...';
                    }
                } else {
                    const saveDraftBtn = document.getElementById('saveDraftBtn');
                    if (saveDraftBtn) {
                        saveDraftBtn.disabled = true;
                        saveDraftBtn.innerHTML = '<i class="fe fe-loader spinning me-1"></i> Saving...';
                    }
                }
            });
        });
    </script>

    <style>
        .spinning {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</x-app-layout>

