@extends('admin.layouts.admin')

@section('title', 'Edit Pharmacy User')
@section('page-title', 'Edit Pharmacy User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pharmacy.index') }}">Pharmacy Users</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Pharmacy User Details</h3>
            </div>
            <form action="{{ route('admin.pharmacy.update', $pharmacyUser->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pharmacyUser->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $pharmacyUser->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="facility_id">Assigned Facility</label>
                        <select name="facility_id" id="facility_id" class="form-control @error('facility_id') is-invalid @enderror" required>
                            <option value="">Select Facility...</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}" {{ old('facility_id', $pharmacyUser->facility_id) == $facility->id ? 'selected' : '' }}>
                                    {{ $facility->name }} ({{ $facility->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('facility_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <hr>
                    <div class="alert alert-info py-2">
                        <small><i class="fas fa-info-circle mr-1"></i> Leave password fields empty to keep current password.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min 8 characters">
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-warning px-5">Update Pharmacy User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
