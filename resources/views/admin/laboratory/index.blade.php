@extends('admin.layouts.admin')

@section('title', 'Manage Laboratory Staff')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold mb-0 text-teal" style="color: #0d9488;">Laboratory Staff</h3>
        <a href="{{ route('admin.laboratory.create') }}" class="btn btn-teal text-white rounded-pill px-4" style="background-color: #0d9488;">
            <i class="bi bi-plus-circle me-1"></i> Add Laboratory Staff
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                        <tr>
                            <td class="fw-bold">{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No laboratory staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $staffMembers->links() }}
        </div>
    </div>
</div>
@endsection
