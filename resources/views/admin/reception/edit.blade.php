@extends('admin.layouts.admin')

@section('title', 'Edit Receptionist')
@section('page-title', 'Edit Receptionist Account')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reception.index') }}">Receptionists</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Update Details: {{ $receptionist->name }}</h3>
            </div>
            <form action="{{ route('admin.reception.update', $receptionist->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $receptionist->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $receptionist->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $receptionist->phone) }}">
                    </div>
                    <div class="form-group">
                        <label for="status">Account Status *</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ $receptionist->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$receptionist->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="mt-4 border-top pt-3">
                        <h6 class="text-muted"><i class="fas fa-key mr-1"></i> Change Password (optional)</h6>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            <small class="form-text text-muted">Leave blank to keep existing password.</small>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Update Account</button>
                    <a href="{{ route('admin.reception.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
