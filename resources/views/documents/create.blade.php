<x-app-layout>
    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-plus-circle me-1"></i> Create New Document
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('documents.index') }}" class="btn btn-sm btn-light">
                <i class="fe fe-arrow-left me-1"></i> Back to Documents
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
            <form method="POST" action="{{ route('documents.store') }}" id="createDocumentForm">
                @csrf
                <div class="card shadow-none border">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fe fe-file-text me-2"></i>Document Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Document Type -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="type" class="form-label">
                                        Document Type <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-file"></i>
                                        </span>
                                        <select class="form-control @error('type') is-invalid @enderror"
                                            name="type"
                                            id="type"
                                            required>
                                            <option value="" disabled {{ (old('type') || isset($selectedType)) ? '' : 'selected' }}>
                                                -- Select Document Type --
                                            </option>
                                            @foreach($availableTypes as $type => $name)
                                                <option value="{{ $type }}" 
                                                    {{ old('type', $selectedType ?? '') == $type ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select the type of document you want to create</small>
                                </div>
                            </div>

                            <!-- Title (Optional) -->
                            <div class="col-md-6 mb-3">
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
                                            placeholder="Enter document title"
                                            value="{{ old('title') }}">
                                    </div>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank to use "Untitled"</small>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="content" class="form-label">
                                        Content <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                        name="content"
                                        id="content"
                                        rows="15"
                                        placeholder="Enter your document content here..."
                                        required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 10 characters required</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">
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
            const form = document.getElementById('createDocumentForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const clickedButton = e.submitter;
                const action = clickedButton?.value || 'draft';
                
                if (action === 'submit') {
                    if (!confirm('Are you sure you want to submit this document? Once submitted, it cannot be edited.')) {
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

