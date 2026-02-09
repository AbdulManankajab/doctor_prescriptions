@extends('admin.layouts.admin')

@section('title', 'Default Notes')
@section('page-title', 'Default Prescription Notes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Default Notes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Default Notes</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.defaults.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Note
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Note Text</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($defaults as $default)
                        <tr>
                            <td>{{ $default->id }}</td>
                            <td>{{ Str::limit($default->detail_text, 100) }}</td>
                            <td>
                                <a href="{{ route('admin.defaults.edit', $default->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.defaults.destroy', $default->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No default notes found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $defaults->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
