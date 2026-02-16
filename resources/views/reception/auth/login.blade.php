<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception Login - Hospital System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #fff;
            padding: 40px 30px 20px;
            text-align: center;
        }
        .login-body {
            background-color: #fff;
            padding: 20px 30px 40px;
        }
        .btn-primary {
            background-color: #3a7bd5;
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #2f69c5;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="mb-3">
            <i class="bi bi-person-workspace fs-1 text-primary"></i>
        </div>
        <h3 class="fw-bold text-primary">Reception Portal</h3>
        <p class="text-muted">Register patients and manage visits</p>
    </div>
    <div class="login-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reception.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-bold">Email Address</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="admin@hospital.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">Password</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="********" required>
                </div>
            </div>
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted" for="remember">Remember me</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow-sm rounded-pill">
                <i class="bi bi-box-arrow-in-right me-2"></i> Access Panel
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
