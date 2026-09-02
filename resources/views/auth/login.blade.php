<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TVET E-Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #1B3A4B;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem;
            max-width: 420px;
            margin: auto;
            width: 100%;
        }
        .btn-teal { background: #2E7D6B; border-color: #2E7D6B; color: #fff; }
        .btn-teal:hover { background: #256b5c; color: #fff; }
        .demo-box { background: #E8F3F0; border-radius: 8px; padding: .85rem 1rem; font-size: .85rem; }
    </style>
</head>
<body>
<div class="login-card shadow">
    <div class="text-center mb-4">
        <i class="bi bi-mortarboard-fill" style="font-size: 2.25rem; color:#2E7D6B;"></i>
        <h4 class="fw-bold mt-2 mb-0">TVET E-Portfolio</h4>
        <p class="text-muted small">Automated Practical Assessment System</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-teal w-100 fw-semibold">Sign In</button>
    </form>

    <div class="demo-box mt-4">
        <strong>Demo logins</strong> (password: <code>password</code>)<br>
        Admin: admin@tvet.test<br>
        Instructor: instructor1@tvet.test<br>
        Student: student1@tvet.test
    </div>
</div>
</body>
</html>
