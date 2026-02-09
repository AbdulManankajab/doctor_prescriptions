@extends('admin.layouts.admin')

@section('title', 'Medicines')
@section('page-title', 'Medicines Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Medicines</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Medicines</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.medicines.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Medicine
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Dosage Options</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->id }}</td>
                            <td>{{ $medicine->name }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($medicine->type) }}</span></td>
                            <td>{{ $medicine->dosage_options ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.medicines.edit', $medicine->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.medicines.destroy', $medicine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No medicines found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $medicines->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
