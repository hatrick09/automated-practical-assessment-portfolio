<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>@yield('title', 'Dashboard') - TVET E-Portfolio</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --navy: #1B3A4B;
            --teal: #2E7D6B;
            --teal-light: #E8F3F0;
            --bg: #F7F9F8;
            --border: #DCE3E1;
        }
        * { box-sizing: border-box; }
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
        }

        /* ---------- Top bar (mobile only) ---------- */
        .mobile-topbar {
            display: none;
            position: sticky;
            top: 0;
            z-index: 1040;
            background: var(--navy);
            color: #fff;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
        }
        .mobile-topbar .brand-mini {
            font-weight: 700;
            font-size: 1rem;
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .menu-toggle-btn {
            background: rgba(255,255,255,0.12);
            border: none;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            background: var(--navy);
            min-height: 100vh;
            width: 240px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            padding: 1.25rem 0.75rem;
            z-index: 1050;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .sidebar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.5rem 0.75rem 1.5rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            margin-bottom: 0.15rem;
            font-size: 0.93rem;
        }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-section {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 1rem 0.85rem 0.35rem;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1045;
        }

        .main-content {
            margin-left: 240px;
            padding: 1.75rem 2rem;
            min-width: 0; /* prevents flex/grid children forcing overflow */
        }

        /* ---------- Mobile layout ---------- */
        @media (max-width: 991.98px) {
            .mobile-topbar { display: flex; }
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.22s ease;
                width: 82%;
                max-width: 300px;
                box-shadow: 4px 0 24px rgba(0,0,0,0.25);
            }
            .sidebar.is-open { transform: translateX(0); }
            .sidebar-backdrop.is-open { display: block; }
            .main-content {
                margin-left: 0;
                padding: 1.1rem 0.9rem;
            }
        }

        @media (max-width: 575.98px) {
            .main-content { padding: 0.9rem 0.75rem; }
            .section-title, h1.section-title { font-size: 18px !important; }
            .stat-counter { font-size: 1.25rem !important; }
            .card-tvet { padding: 1rem !important; }
            .avatar-circle { width: 48px !important; height: 48px !important; font-size: 1rem !important; }
            table { font-size: 0.85rem; }
            .btn { font-size: 0.85rem; }
        }

        /* ---------- Mobile: bigger, more legible form controls ---------- */
        /* Bootstrap's "-sm" inputs (used throughout inline edit rows and the
           scoring form) are fine on a desktop but too small to read/tap on a
           phone. Force every input/select back up to a comfortable, tappable
           size on mobile, and use 16px+ font so iOS Safari doesn't auto-zoom
           the page when a field is focused (that auto-zoom is also why some
           content, like an Approve button, could seem to "disappear" after
           tapping a nearby field). */
        @media (max-width: 767.98px) {
            .form-control, .form-select,
            .form-control-sm, .form-select-sm,
            select.form-select-sm, input.form-control-sm {
                font-size: 16px !important;
                padding: 0.55rem 0.7rem !important;
                min-height: 44px !important;
            }
            textarea.form-control { min-height: 90px !important; }
            .form-check-input { width: 1.25em; height: 1.25em; }
            .btn { min-height: 42px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .btn-sm { min-height: 38px; }
        }

        /* ---------- Mobile: keep the last table column (usually the action
           button, e.g. "Approve", "Save", "Delete") permanently visible on
           the right edge of a horizontally-scrolling table, instead of it
           being scrolled out of view with no visual hint it's there. ---------- */
        @media (max-width: 767.98px) {
            .table-responsive { position: relative; }
            .table-tvet td:last-child, .table-tvet th:last-child {
                position: sticky;
                right: 0;
                background: #fff;
                box-shadow: -8px 0 8px -6px rgba(0,0,0,0.18);
                white-space: nowrap;
            }
            .table-tvet thead th:last-child { background: var(--teal-light); }
        }

        .card-tvet {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.25rem;
            max-width: 100%;
        }
        h1, h2, h3, .section-title {
            font-weight: 700;
        }
        .section-title { font-size: 20px; }
        .badge-teal {
            background: var(--teal-light);
            color: var(--teal);
            font-weight: 600;
            border-radius: 6px;
            padding: 0.35em 0.65em;
            white-space: nowrap;
        }
        .btn-teal {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff;
        }
        .btn-teal:hover { background: #256b5c; color: #fff; }
        .btn-outline-teal {
            border-color: var(--teal);
            color: var(--teal);
        }
        .btn-outline-teal:hover { background: var(--teal); color: #fff; }
        .table-tvet thead { background: var(--teal-light); }
        .table-responsive { -webkit-overflow-scrolling: touch; }
        .stat-counter { font-size: 1.6rem; font-weight: 700; color: var(--navy); }
        .progress { background: var(--border); border-radius: 6px; height: 8px; }
        .progress-bar { background: var(--teal); }
        .verified-badge {
            background: var(--teal-light);
            color: var(--teal);
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 0.25em 0.75em;
            display: inline-block;
            white-space: nowrap;
        }
        .avatar-circle {
            width: 56px; height: 56px; border-radius: 50%;
            background: var(--teal); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.2rem;
            flex-shrink: 0;
        }
        /* Prevent wide tables/forms from blowing out the page width on phones */
        img, table { max-width: 100%; }
        .row { --bs-gutter-x: 1rem; }
    </style>
    @stack('styles')
</head>
<body>
<div class="mobile-topbar">
    <button class="menu-toggle-btn" type="button" onclick="tvetToggleSidebar(true)" aria-label="Open menu">
        <i class="bi bi-list"></i>
    </button>
    <div class="brand-mini"><i class="bi bi-mortarboard-fill me-2"></i>TVET E-Portfolio</div>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="tvetToggleSidebar(false)"></div>

<div class="d-flex">
    <nav class="sidebar" id="tvetSidebar">
        <div class="brand"><i class="bi bi-mortarboard-fill me-2"></i>TVET E-Portfolio</div>
        <ul class="nav flex-column">
            @auth
                @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <div class="nav-section">Academic Structure</div>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}"><i class="bi bi-diagram-3-fill me-2"></i>Departments</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.programmes.*') ? 'active' : '' }}" href="{{ route('admin.programmes.index') }}"><i class="bi bi-collection-fill me-2"></i>Programmes</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.academic.*') ? 'active' : '' }}" href="{{ route('admin.academic.index') }}"><i class="bi bi-calendar3 me-2"></i>Academic Years</a></li>
                    <div class="nav-section">Management</div>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people-fill me-2"></i>Users</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.trades.*') ? 'active' : '' }}" href="{{ route('admin.trades.index') }}"><i class="bi bi-tools me-2"></i>Trades</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}"><i class="bi bi-journal-bookmark-fill me-2"></i>Courses</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.rubrics.*') ? 'active' : '' }}" href="{{ route('admin.rubrics.index') }}"><i class="bi bi-card-checklist me-2"></i>Rubrics</a></li>
                    <div class="nav-section">Insights</div>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart-fill me-2"></i>Reports</a></li>
                @elseif(auth()->user()->isHod())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('hod.dashboard') ? 'active' : '' }}" href="{{ route('hod.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Department Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('hod.approvals.*') ? 'active' : '' }}" href="{{ route('hod.approvals.index') }}"><i class="bi bi-check2-square me-2"></i>Approvals</a></li>
                    <div class="nav-section">As Instructor</div>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('instructor.assessments.*') ? 'active' : '' }}" href="{{ route('instructor.assessments.index') }}"><i class="bi bi-pencil-square me-2"></i>Score Assessments</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('instructor.submissions.*') ? 'active' : '' }}" href="{{ route('instructor.submissions.index') }}"><i class="bi bi-inbox-fill me-2"></i>Student Submissions</a></li>
                @elseif(auth()->user()->isInstructor())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}" href="{{ route('instructor.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('instructor.assessments.*') ? 'active' : '' }}" href="{{ route('instructor.assessments.index') }}"><i class="bi bi-pencil-square me-2"></i>Score Assessments</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('instructor.submissions.*') ? 'active' : '' }}" href="{{ route('instructor.submissions.index') }}"><i class="bi bi-inbox-fill me-2"></i>Student Submissions</a></li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.dashboard') || request()->routeIs('student.portfolio') ? 'active' : '' }}" href="{{ route('student.portfolio') }}"><i class="bi bi-person-badge-fill me-2"></i>My Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.submissions.*') ? 'active' : '' }}" href="{{ route('student.submissions.index') }}"><i class="bi bi-upload me-2"></i>My Submissions</a></li>
                @endif
                <div class="nav-section">Account</div>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link border-0 bg-transparent w-100 text-start" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout ({{ auth()->user()->name }})</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>

    <main class="main-content flex-grow-1">
        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('share_url'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Public link: <a href="{{ session('share_url') }}" target="_blank">{{ session('share_url') }}</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function tvetToggleSidebar(open) {
        const sidebar = document.getElementById('tvetSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
    }
    // Close the mobile menu automatically after tapping a nav link.
    document.querySelectorAll('#tvetSidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 991.98) tvetToggleSidebar(false);
        });
    });
    // If the viewport is resized up to desktop width, make sure mobile state is cleared.
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991.98) tvetToggleSidebar(false);
    });
</script>
@stack('scripts')
</body>
</html>
