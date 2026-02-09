@extends('layouts.doctor')

@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-4">
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-person-badge me-2"></i> My Profile
                </h4>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="show"></button>
                    </div>
                @endif

                <form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4 align-items-center">
                        <div class="col-md-3 text-center">
                            @if($doctor->profile_picture)
                                <img src="{{ asset('public/storage/' . $doctor->profile_picture) }}" alt="Profile" class="rounded-circle img-thumbnail shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 120px; height: 120px;">
                                    <i class="bi bi-person text-muted" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            <div class="mt-2">
                                <label for="profile_picture" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    Change Photo
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h5 class="fw-bold mb-1">{{ $doctor->name }}</h5>
                            <p class="text-muted mb-0">{{ $doctor->specialization ?: 'Specialization not set' }}</p>
                            <p class="small text-muted">{{ $doctor->email }}</p>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="row g-3">
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $doctor->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Email (View only)</label>
                            <input type="email" class="form-control bg-light" value="{{ $doctor->email }}" readonly disabled>
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $doctor->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Specialization</label>
                            <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $doctor->specialization) }}" placeholder="e.g. Cardiologist">
                            @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Qualification</label>
                            <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $doctor->qualification) }}" placeholder="e.g. MBBS, MD">
                            @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Years of Experience</label>
                            <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', $doctor->experience_years) }}" min="0">
                            @error('experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 text-start">
                            <label class="form-label fw-semibold">Clinic/Hospital Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $doctor->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 text-start">
                            <label class="form-label fw-semibold">Short Biography</label>
                            <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3">{{ old('bio', $doctor->bio) }}</textarea>
                            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 text-start">
                            <hr class="my-4 opacity-25">
                            <h6 class="fw-bold mb-3">Change Password (Leave blank to keep current)</h6>
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 text-start">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">
                            <i class="bi bi-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
