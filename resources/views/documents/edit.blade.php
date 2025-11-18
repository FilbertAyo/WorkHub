<x-app-layout>
    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        <i class="fe fe-edit me-1"></i> Edit Document
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('documents.show', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}" class="btn btn-sm btn-info text-white me-1">
                <i class="fe fe-eye me-1"></i> View Document
            </a>
            <a href="{{ route('documents.index') }}" class="btn btn-sm btn-light">
                <i class="fe fe-arrow-left me-1"></i> Back to Documents
            </a>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('documents.update', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}" id="editDocumentForm">
                @csrf
                @method('PUT')
                <div class="card shadow-none border">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fe fe-file-text me-2"></i>Edit Document
                            <span class="badge badge-info ml-2">{{ $document->type_name }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Document Type (Read-only) -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Document Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-file"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ $document->type_name }}"
                                            disabled>
                                    </div>
                                    <small class="form-text text-muted">Document type cannot be changed</small>
                                </div>
                            </div>

                            <!-- State (Read-only) -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fe fe-info"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ $document->state_name }}"
                                            disabled>
                                    </div>
                                    <small class="form-text text-muted">Current document state</small>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        Title
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
                                            value="{{ old('title', $document->getDataField('title', '')) }}">
                                    </div>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
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
                                        required>{{ old('content', $document->getDataField('content', '')) }}</textarea>
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
                            <a href="{{ route('documents.show', \Vinkla\Hashids\Facades\Hashids::encode($document->id)) }}" class="btn btn-secondary">
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
            const form = document.getElementById('editDocumentForm');
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

