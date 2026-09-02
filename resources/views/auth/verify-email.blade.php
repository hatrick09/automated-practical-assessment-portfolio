<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="mb-3">Verify your email</h3>
                    <p class="text-muted">Before continuing, please verify your email address.</p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">A new verification link has been sent.</div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button class="btn btn-primary w-100">Resend verification email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
