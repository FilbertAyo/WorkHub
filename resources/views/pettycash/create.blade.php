<x-app-layout>

    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">
                        New PettyCash Request
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <form method="POST" action="{{ route('petty.store') }}" enctype="multipart/form-data" id="pettyForm">
        @csrf

        <div class="card shadow-none border">
            <div class="card-header">
                <div class="card-title">Request Info</div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="department_id" value="{{ Auth::user()->department_id }}">

                    <div class="col-md-6 col-lg-6 mt-3">
                        <div class="form-group">
                            <label for="request_type">Request Type: <span class="text-danger">*</span></label>
                            <select name="request_type" id="request_type" class="form-control" required>
                                <option value="Petty Cash" {{ old('request_type') == 'Petty Cash' ? 'selected' : '' }}>
                                    Petty Cash
                                </option>
                                <option value="Reimbursement" {{ old('request_type') == 'Reimbursement' ? 'selected' : '' }} disabled>
                                    Reimbursement
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 mt-3">
                        <div class="form-group">
                            <label for="request_for">Request for <span class="text-danger">*</span></label>
                            <select name="request_for" class="form-control" required>
                                <option value="" disabled {{ old('request_for') ? '' : 'selected' }}>-- Select Reason --</option>
                                <option value="Sales Delivery" {{ old('request_for') == 'Sales Delivery' ? 'selected' : '' }}>
                                    Sales Delivery</option>
                                <option value="Transport" {{ old('request_for') == 'Transport' ? 'selected' : '' }}>
                                    Transport</option>
                                <option value="Office Supplies" {{ old('request_for') == 'Office Supplies' ? 'selected' : '' }}>
                                    Office Supplies</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 mt-3">
                        <div class="form-group">
                            <label for="amount">Amount Needed: <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required
                                value="{{ old('amount') }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 mt-3">
                        <div class="form-group">
                            <label for="reason">Description: <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" cols="50" required>{{ old('reason') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-action m-3">
                <button type="submit" class="btn btn-primary">
                    Submit Request
                </button>
            </div>
        </div>
    </form>

</x-app-layout>
