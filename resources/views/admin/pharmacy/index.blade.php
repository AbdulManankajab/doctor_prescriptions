@extends('admin.layouts.admin')

@section('title', 'Pharmacy Users')
@section('page-title', 'Pharmacy Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pharmacy Users</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pharmacy Management</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Pharmacy User
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Facility</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pharmacyUsers as $user)
                        <tr>
                            <td>{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->facility)
                                    {{ $user->facility->name }} <br>
                                    <small class="text-muted">{{ $user->facility->type }}</small>
                                @else
                                    <span class="text-muted small">Not Assigned</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.pharmacy.edit', $user->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.pharmacy.destroy', $user->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this pharmacy user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No pharmacy users registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $pharmacyUsers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
