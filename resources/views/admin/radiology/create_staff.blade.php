@extends('admin.layouts.admin')

@section('title', 'Add Radiology Staff')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.radiology.index') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Staff List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white p-4 border-0">
                <h4 class="fw-bold mb-0">Add Radiology Staff</h4>
            </div>
            <div class="card-body p-4 pt-0">
                <hr class="mt-0 mb-4">
                <form action="{{ route('admin.radiology.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address *</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password *</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-person-plus me-1"></i> Create Staff Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
