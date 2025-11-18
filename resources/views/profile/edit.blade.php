<x-app-layout>


    <div class="row align-items-center mb-3 border-bottom no-gutters">
        <div class="col">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="branches-tab" data-toggle="tab" href="#branches" role="tab"
                        aria-controls="branches" aria-selected="true">
                        Profile Settings
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-white shadow-sm" onclick="reloadPage()">
                <i class="fe fe-refresh-ccw mr-2"></i>Refresh
            </button>
        </div>
    </div>


    <!-- Profile Information Section -->
    <div class="mb-4">
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Update Password Section -->
    <div class="card shadow-none border mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">
                <div class="icon-square bg-success-light rounded mr-3">
                    <i class="fe fe-lock text-success"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">Update Password</h5>
                    <p class="text-muted small mb-0">Ensure your account is using a long, random password to stay secure
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @include('profile.partials.update-password-form')
        </div>
    </div>

 

    <style>
        .icon-square {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-success-light {
            background-color: rgba(16, 185, 129, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .btn-white {
            background-color: #fff;
            border: 1px solid #e5e7eb;
        }

        .btn-white:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }
    </style>

</x-app-layout>
