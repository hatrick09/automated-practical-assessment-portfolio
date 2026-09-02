<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
        }
        .sidebar {
            background: var(--navy);
            min-height: 100vh;
            width: 240px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            padding: 1.25rem 0.75rem;
            z-index: 1030;
            overflow-y: auto;
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
        .main-content {
            margin-left: 240px;
            padding: 1.75rem 2rem;
        }
        @media (max-width: 767.98px) {
            .sidebar { position: static; width: 100%; min-height: auto; }
            .main-content { margin-left: 0; padding: 1.25rem; }
        }
        .card-tvet {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.25rem;
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
        }
        .avatar-circle {
            width: 56px; height: 56px; border-radius: 50%;
            background: var(--teal); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.2rem;
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <nav class="sidebar">
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
@stack('scripts')
</body>
</html>
