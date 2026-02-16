<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription System - Choice</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --admin-color: #6366f1;
            --doctor-color: #0ea5e9;
            --pharmacy-color: #0d9488;
            --radiology-color: #3b82f6;
            --laboratory-color: #0d9488;
            --reception-color: #3a7bd5;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
        }

        .container {
            max-width: 1200px;
            padding: 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .header-section h1 {
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.025em;
            margin-bottom: 15px;
        }

        .header-section p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .portal-card {
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 24px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none !important;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.2rem;
            transition: all 0.3s ease;
        }

        .doctor-box {
            background-color: rgba(14, 165, 233, 0.1);
            color: var(--doctor-color);
        }

        .admin-box {
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--admin-color);
        }

        .pharmacy-box {
            background-color: rgba(13, 148, 136, 0.1);
            color: var(--pharmacy-color);
        }

        .radiology-box {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--radiology-color);
        }

        .laboratory-box {
            background-color: rgba(13, 148, 136, 0.1);
            color: var(--laboratory-color);
        }

        .portal-card:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        .portal-card h3 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            font-size: 1.4rem;
        }

        .portal-card p {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .btn-portal {
            margin-top: 25px;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .btn-doctor {
            background-color: var(--doctor-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(14, 165, 233, 0.39);
        }

        .btn-doctor:hover {
            background-color: #0284c7;
            color: white;
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.23);
            transform: translateY(-2px);
        }

        .btn-admin {
            background-color: var(--admin-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
        }

        .btn-admin:hover {
            background-color: #4f46e5;
            color: white;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.23);
            transform: translateY(-2px);
        }

        .btn-pharmacy {
            background-color: var(--pharmacy-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(13, 148, 136, 0.39);
        }
        
        .btn-pharmacy:hover {
            background-color: #0f766e;
            color: white;
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.23);
            transform: translateY(-2px);
        }

        .btn-radiology {
            background-color: var(--radiology-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
        }

        .btn-radiology:hover {
            background-color: #2563eb;
            color: white;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.23);
            transform: translateY(-2px);
        }

        .btn-laboratory {
            background-color: var(--laboratory-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(13, 148, 136, 0.39);
        }

        .btn-laboratory:hover {
            background-color: #0f766e;
            color: white;
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.23);
            transform: translateY(-2px);
        }

        .reception-box {
            background-color: rgba(58, 123, 213, 0.1);
            color: var(--reception-color);
        }

        .btn-reception {
            background-color: var(--reception-color);
            color: white;
            border: none;
            box-shadow: 0 4px 14px 0 rgba(58, 123, 213, 0.39);
        }

        .btn-reception:hover {
            background-color: #2f69c5;
            color: white;
            box-shadow: 0 6px 20px rgba(58, 123, 213, 0.23);
            transform: translateY(-2px);
        }

        .portal-card:hover .btn-portal {
            transform: translateY(-3px);
        }

        .badge-new {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ec4899;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1 class="display-4"><i class="bi bi-heart-pulse-fill text-danger"></i> Prescription System</h1>
        <p>Please select your portal to continue</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Admin Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('admin')
                    <span class="badge-new bg-success">Logged In</span>
                @endauth
                <div>
                    <div class="icon-box admin-box">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3>Admin Panel</h3>
                    <p>Global oversight of doctors, clinics, system settings, and staff accounts.</p>
                </div>
                @auth('admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-portal btn-admin">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn btn-portal btn-admin">
                        Access Admin <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Doctor Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('doctor')
                    <span class="badge-new bg-success">Logged In</span>
                @else
                    <span class="badge-new">Active</span>
                @endauth
                <div>
                    <div class="icon-box doctor-box">
                        <i class="bi bi-person-heart"></i>
                    </div>
                    <h3>Doctor Portal</h3>
                    <p>Manage patients, create digital prescriptions, and request diagnostics.</p>
                </div>
                @auth('doctor')
                    <a href="{{ route('doctor.dashboard') }}" class="btn btn-portal btn-doctor">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('doctor.login') }}" class="btn btn-portal btn-doctor">
                        Access Portal <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Pharmacy Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('pharmacy')
                    <span class="badge-new bg-success">Logged In</span>
                @endauth
                <div>
                    <div class="icon-box pharmacy-box">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <h3>Hospital Pharmacy</h3>
                    <p>View sent prescriptions, dispense medications, and print records.</p>
                </div>
                @auth('pharmacy')
                    <a href="{{ route('pharmacy.dashboard') }}" class="btn btn-portal btn-pharmacy">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('pharmacy.login') }}" class="btn btn-portal btn-pharmacy">
                        Access Pharmacy <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Radiology Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('radiology')
                    <span class="badge-new bg-success">Logged In</span>
                @endauth
                <div>
                    <div class="icon-box radiology-box">
                        <i class="bi bi-x-diamond-fill"></i>
                    </div>
                    <h3>Radiology Dept</h3>
                    <p>Access X-Ray requests, upload diagnostic images, and provide reports.</p>
                </div>
                @auth('radiology')
                    <a href="{{ route('radiology.dashboard') }}" class="btn btn-portal btn-radiology">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('radiology.login') }}" class="btn btn-portal btn-radiology">
                        Access X-Ray Portal <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Laboratory Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('laboratory')
                    <span class="badge-new bg-success">Logged In</span>
                @endauth
                <div>
                    <div class="icon-box laboratory-box">
                        <i class="bi bi-water"></i>
                    </div>
                    <h3>Medical Laboratory</h3>
                    <p>Mange blood tests, enter laboratory results, and upload digital findings.</p>
                </div>
                @auth('laboratory')
                    <a href="{{ route('laboratory.dashboard') }}" class="btn btn-portal btn-laboratory">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('laboratory.login') }}" class="btn btn-portal btn-laboratory">
                        Access Lab Portal <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Reception Portal -->
        <div class="col-lg-4 col-md-6">
            <div class="portal-card">
                @auth('reception')
                    <span class="badge-new bg-success">Logged In</span>
                @endauth
                <div>
                    <div class="icon-box reception-box">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <h3>Reception Panel</h3>
                    <p>Register new patients, search records, and create hospital visit tokens.</p>
                </div>
                @auth('reception')
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-portal btn-reception">
                        Go to Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('reception.login') }}" class="btn btn-portal btn-reception">
                        Access Reception <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted small">© 2026 Medical Informatics Solutions. All rights reserved.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
