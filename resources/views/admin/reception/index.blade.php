@extends('admin.layouts.admin')

@section('title', 'Receptionists')
@section('page-title', 'Receptionist Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Receptionists</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reception Staff Accounts</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.reception.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Receptionist
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
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receptionists as $receptionist)
                        <tr>
                            <td>{{ str_pad($receptionist->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $receptionist->name }}</strong></td>
                            <td>{{ $receptionist->email }}</td>
                            <td>{{ $receptionist->phone ?? 'N/A' }}</td>
                            <td>
                                @if($receptionist->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $receptionist->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.reception.edit', $receptionist->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.reception.destroy', $receptionist->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure?');">
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
                            <td colspan="7" class="text-center py-4">No receptionist accounts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $receptionists->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
