<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · FYRIRHYGGJA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #14342e, #1f5049 60%, #2f7a6c); min-height: 100vh; display: flex; align-items: center; font-family: system-ui, sans-serif; }
        .login-card { max-width: 380px; margin: auto; border: none; border-radius: 1.25rem; box-shadow: 0 20px 60px rgba(0,0,0,.35); }
        .brand-gradient { background: linear-gradient(135deg, #fff, #d9ad4f); -webkit-background-clip: text; background-clip: text; color: transparent; font-weight: 800; }
        .btn-brand { background: linear-gradient(135deg, #d9ad4f, #b6862c); border: none; border-radius: 999px; font-weight: 600; color: #14342e; }
        .btn-brand:hover { filter: brightness(1.05); color: #14342e; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card p-4">  
        <div class="text-center mb-4">
            <div class="fs-3">🌿</div>
            <h5 class="brand-gradient mb-0">FYRIRHYGGJA</h5>
            <small class="text-muted">Admin panel</small>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-brand w-100">Sign in</button>
        </form>
    </div>
</div>
</body>
</html>
