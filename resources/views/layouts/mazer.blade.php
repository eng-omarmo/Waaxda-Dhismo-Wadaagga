<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IPAMS Portal')</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    @stack('page-styles')
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="{{ url('/') }}"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo"></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">


                        <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ url('/dashboard') }}" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/dashboard') }}#overview">Overview of Projects</a></li>
                                <li class="submenu-item"><a href="{{ url('/dashboard') }}#approvals">Pending Approvals</a></li>
                                <li class="submenu-item"><a href="{{ url('/dashboard') }}#activity">Recent Activity</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/projects') ? 'active' : '' }}">
                            <a href="{{ url('/admin/projects') }}" class="sidebar-link">
                                <i class="bi bi-briefcase"></i>
                                <span>Project Management</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/projects') }}">All Projects – List &amp; status</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/projects/create') }}#register">Register New Project</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/projects') }}#details">Project Details → Buildings → Units</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/permits') ? 'active' : '' }}">
                            <a href="{{ url('/admin/permits') }}" class="sidebar-link">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Construction Permits</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/permits') }}">Permit Requests – Pending / Approved / Rejected</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/permits') }}#approve">Approve Permits</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/permits') }}#history">Permit History</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/buildings') ? 'active' : '' }}">
                            <a href="{{ url('/admin/buildings') }}" class="sidebar-link">
                                <i class="bi bi-building"></i>
                                <span>Apartments</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/buildings') }}">Buildings – Add / Edit</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/buildings') }}#units">Units – Assign / Transfer / Status</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/licensing') ? 'active' : '' }}">
                            <a href="{{ url('/admin/licensing') }}" class="sidebar-link">
                                <i class="bi bi-patch-check"></i>
                                <span>Licensing &amp; Commercial Approvals</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/licensing') }}">Licenses – Active / Expired / Revoked</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/new-business-license') }}">Issue License</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/licensing') }}#history">License History</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/ownership') ? 'active' : '' }}">
                            <a href="{{ url('/admin/ownership') }}" class="sidebar-link">
                                <i class="bi bi-person-badge"></i>
                                <span>Land Ownership Verification</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/ownership') }}">Ownership Claims – Pending / Verified / Rejected</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/ownership') }}#verify">Verify Owner</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/ownership') }}#history">Ownership History</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/transfers') ? 'active' : '' }}">
                            <a href="{{ url('/admin/transfers') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Property Transfers</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/transfers') }}">Transfer Requests – Requested / Approved / Completed</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/transfers') }}#approve">Approve Transfers</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/transfers') }}#history">Transfer History</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/organizations') ? 'active' : '' }}">
                            <a href="{{ url('/admin/organizations') }}" class="sidebar-link">
                                <i class="bi bi-building-gear"></i>
                                <span>Organizations</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/organizations') }}">All Organizations – Pending / Approved / Rejected</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/organizations') }}#create">Create Organization</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/inspections') || request()->is('admin/audit') ? 'active' : '' }}">
                            <a href="{{ url('/admin/inspections') }}" class="sidebar-link">
                                <i class="bi bi-search"></i>
                                <span>Administration &amp; Monitoring</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/inspections') }}">Inspections – Schedule / Conduct / Remarks</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/audit') }}">Audit Logs – Track User Actions</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/users') || request()->is('admin/roles') ? 'active' : '' }}">
                            <a href="{{ url('/admin/users') }}" class="sidebar-link">
                                <i class="bi bi-people"></i>
                                <span>Users &amp; Access Control</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ route('admin.users.index') }}">All Users – Roles &amp; Status</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/roles') }}">Roles Management – Citizen / Officer / Admin</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub {{ request()->is('admin/reports') ? 'active' : '' }}">
                            <a href="{{ url('/admin/reports') }}" class="sidebar-link">
                                <i class="bi bi-bar-chart"></i>
                                <span>Reports &amp; Analytics</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ url('/admin/reports') }}#projects">Project Reports</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/reports') }}#permits">Permit &amp; License Statistics</a></li>
                                <li class="submenu-item"><a href="{{ url('/admin/reports') }}#ownership">Ownership &amp; Transfer Trends</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <h3>@yield('page-heading')</h3>
            </div>
            <div class="page-content">
                @yield('content')
            </div>
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>{{ date('Y') }} &copy; Mazer</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a href="http://ahmadsaugi.com">A. Saugi</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @stack('page-scripts')
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
