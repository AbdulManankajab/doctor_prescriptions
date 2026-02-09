@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-6">
            <h1 class="h3 mb-0 text-gray-800">Facilities</h1>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Facility
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Province/District</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facilities as $facility)
                        <tr>
                            <td class="text-center">
                                @if($facility->logo_path)
                                    <img src="{{ asset('public/storage/' . $facility->logo_path) }}?v={{ time() }}" alt="Logo" class="img-thumbnail" style="height: 50px;">
                                @else
                                    <span class="badge bg-secondary">No Logo</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $facility->name }}</div>
                                <small class="text-muted">Lic: {{ $facility->license_number }}</small>
                            </td>
                            <td>{{ $facility->type }}</td>
                            <td>
                                <div>{{ $facility->province }}</div>
                                <small class="text-muted">{{ $facility->district }}</small>
                            </td>
                            <td>
                                <div><i class="fas fa-phone me-1"></i> {{ $facility->phone }}</div>
                                @if($facility->email)
                                    <div><i class="fas fa-envelope me-1"></i> {{ $facility->email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($facility->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No facilities found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
