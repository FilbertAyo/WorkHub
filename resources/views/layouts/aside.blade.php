<aside class="sidebar-left border-right bg-white shadow-none border" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>

    <nav class="vertnav navbar navbar-light">

        <!-- Logo -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('image/logo-dark.png') }}" class="navbar-brand-img" alt="Logo"
                    style="height: 60px">
            </a>
        </div>

        <!-- Dashboard -->
        <ul class="navbar-nav flex-fill w-100">
            <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="fe fe-home fe-16"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>
          <!-- plan,reports, minutes -->
          <p class="text-muted nav-heading mt-4 mb-1"><span>Work Hub</span></p>
          <ul class="navbar-nav flex-fill w-100">
            @php
                $user = Auth::user();
                $showEmployeeDashboard = $user->hasRole('employee') || ($user->hasRole('admin') && !$user->hasAnyRole(['reviewer', 'minutes_preparer']));
            @endphp
            @if($showEmployeeDashboard)
                <li class="nav-item {{ Request::routeIs('documents.employee-dashboard') ? 'active' : '' }}">
                    <a href="{{ route('documents.employee-dashboard') }}" class="nav-link">
                        <i class="fe fe-home fe-16"></i>
                        <span class="ml-3 item-text">My Dashboard</span>
                        @php
                            $pendingCount = \App\Models\Document::drafts()->forUser(Auth::id())->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge badge-warning ml-2">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
            @endif
            <li class="nav-item {{ Request::routeIs('documents.index') ? 'active' : '' }}">
                <a href="{{ route('documents.index') }}" class="nav-link">
                    <i class="fe fe-list fe-16"></i>
                    <span class="ml-3 item-text">All Documents</span>
                </a>
            </li>

            @php
                $user = Auth::user();
                $availableTypes = \App\Models\Document::getAvailableTypesForUser($user);
            @endphp

            @if(!empty($availableTypes))
                <li class="nav-item dropdown {{ Request::routeIs('documents.create') ? 'active' : '' }}">
                    <a href="#createDocumentMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                        <i class="fe fe-plus-circle fe-16"></i>
                        <span class="ml-3 item-text">Create Document</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="createDocumentMenu">
                        @if(isset($availableTypes['weekly_plan']))
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="{{ route('documents.create') }}?type=weekly_plan">
                                    <i class="fe fe-calendar me-1"></i>
                                    <span class="sub-item">Weekly Plan</span>
                                </a>
                            </li>
                        @endif
                        @if(isset($availableTypes['weekly_report']))
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="{{ route('documents.create') }}?type=weekly_report">
                                    <i class="fe fe-file-text me-1"></i>
                                    <span class="sub-item">Weekly Report</span>
                                </a>
                            </li>
                        @endif
                        @if(isset($availableTypes['monthly_report']))
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="{{ route('documents.create') }}?type=monthly_report">
                                    <i class="fe fe-bar-chart me-1"></i>
                                    <span class="sub-item">Monthly Report</span>
                                </a>
                            </li>
                        @endif
                        @if(isset($availableTypes['weekly_minutes']))
                            <li class="nav-item">
                                <a class="nav-link pl-3" href="{{ route('documents.create') }}?type=weekly_minutes">
                                    <i class="fe fe-clipboard me-1"></i>
                                    <span class="sub-item">Weekly Minutes</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(Auth::user()->hasAnyRole(['reviewer', 'admin']))
                <li class="nav-item {{ Request::routeIs('documents.reviewer-dashboard') ? 'active' : '' }}">
                    <a href="{{ route('documents.reviewer-dashboard') }}" class="nav-link">
                        <i class="fe fe-clipboard fe-16"></i>
                        <span class="ml-3 item-text">Submitted Documents</span>
                        @php
                            $submittedCount = \App\Models\Document::submitted()->count();
                        @endphp
                        @if($submittedCount > 0)
                            <span class="badge badge-primary ml-2">{{ $submittedCount }}</span>
                        @endif
                    </a>
                </li>
            @endif
          </ul>

          <!-- Minutes Section (for minutes_preparer) -->
          @if(Auth::user()->hasAnyRole(['minutes_preparer', 'admin']))
          <p class="text-muted nav-heading mt-4 mb-1"><span>Minutes Management</span></p>
          <ul class="navbar-nav flex-fill w-100">
            <li class="nav-item {{ Request::routeIs('minutes.index') ? 'active' : '' }}">
                <a href="{{ route('minutes.index') }}" class="nav-link">
                    <i class="fe fe-clipboard fe-16"></i>
                    <span class="ml-3 item-text">My Minutes</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('minutes.create') ? 'active' : '' }}">
                <a href="{{ route('minutes.create') }}" class="nav-link">
                    <i class="fe fe-plus-circle fe-16"></i>
                    <span class="ml-3 item-text">Create Minutes</span>
                </a>
            </li>
          </ul>
          @endif

        <!-- Petty Cash -->
        <p class="text-muted nav-heading mt-4 mb-1"><span>Petty Cash</span></p>
        <ul class="navbar-nav flex-fill w-100">
            <li class="nav-item {{ Request::routeIs('petty.index') ? 'active' : '' }}">
                <a href="{{ route('petty.index') }}" class="nav-link">
                    <i class="fe fe-file-text fe-16"></i>
                    <span class="ml-3 item-text">Petty Cash Requests</span>
                </a>
            </li>


        @can('first pettycash approval')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('petty.list') }}">
                    <i class="fe fe-users fe-16"></i>
                    <span class="ml-3 item-text">Approvals</span>
                    @if ($pendingPettiesCount > 0)
                        <span class="badge badge-danger">{{ $pendingPettiesCount }}</span>
                    @endif
                </a>
            </li>
        @endcan

        @can('view cashflow movements')
            <ul class="navbar-nav flex-fill w-100">
                <li class="nav-item dropdown
                    {{ Request::routeIs('petty.cashier') || Request::routeIs('deposit.index') || Request::routeIs('cashflow.index') ? 'active' : '' }}">
                    <a href="#financeTransactionsMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                        <i class="fe fe-credit-card fe-16"></i>
                        <span class="ml-3 item-text">Finance & Transactions</span>
                    </a>
                    <ul class="collapse list-unstyled pl-4 w-100" id="financeTransactionsMenu">
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="{{ route('petty.cashier') }}">
                                <span class="ml-1 item-text">Payments</span>
                                @if ($approvedPettiesCount > 0)
                                    <span class="badge badge-success">{{ $approvedPettiesCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="{{ route('deposit.index') }}">
                                <span class="sub-item">Cash Management</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link pl-3" href="{{ route('cashflow.index') }}">
                                <span class="sub-item">Cash Flow</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endcan


        @can('view reports')
            <p class="text-muted nav-heading mt-4 mb-1"><span>Reports and Settings</span></p>
            <ul class="navbar-nav flex-fill w-100">
                <li class="nav-item {{ Request::routeIs('reports') ? 'active' : '' }}">
                    <a href="{{ route('reports') }}" class="nav-link">
                        <i class="fe fe-box"></i>
                        <span class="ml-3 item-text">Reports</span>
                    </a>
                </li>
            </ul>
        @endcan


        @can('view settings')
            <ul class="navbar-nav flex-fill w-100">
                <li
                    class="nav-item dropdown {{ Request::routeIs('admin.index') || Request::routeIs('departments') || Request::routeIs('settings.*') ? 'active' : '' }}">
                    <a href="#settingsMenu" data-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle nav-link">
                        <i class="fe fe-settings"></i>
                        <span class="ml-3 item-text">Settings</span>
                    </a>

                    <ul class="collapse list-unstyled pl-4 w-100" id="settingsMenu">
                        <li class="nav-item {{ Request::routeIs('admin.index') ? 'active' : '' }}">
                            <a class="nav-link pl-3" href="{{ route('admin.index') }}">
                                <i class="fe fe-users"></i>
                                <span class="sub-item">Users</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::routeIs('departments') ? 'active' : '' }}">
                            <a class="nav-link pl-3" href="{{ route('departments') }}">
                                <i class="fe fe-briefcase"></i>
                                <span class="sub-item">Departments</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::routeIs('notification.index') ? 'active' : '' }}">
                            <a class="nav-link pl-3" href="{{ route('notification.index') }}">
                                <i class="fe fe-bell"></i>
                                <span class="sub-item">Notifications</span>
                            </a>
                        </li>


                    </ul>
                </li>
            </ul>
        @endcan


        <div class="btn-box w-100 mt-5 mb-3 px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-block">
                    <i class="fe fe-log-out fe-12 mx-2"></i>
                    <span class="small">Log out</span>
                </button>
            </form>
        </div>

    </nav>
</aside>
