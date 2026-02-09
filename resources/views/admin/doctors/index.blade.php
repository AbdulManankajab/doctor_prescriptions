@extends('admin.layouts.admin')

@section('title', 'Doctors')
@section('page-title', 'Doctor Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Doctors</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Doctors</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Doctor
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
                            <th>Specialization</th>
                            <th>Facility</th>
                            <th>Patients</th>
                            <th>Prescriptions</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors as $doctor)
                        <tr>
                            <td>{{ str_pad($doctor->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td><strong>{{ $doctor->name }}</strong></td>
                            <td>{{ $doctor->email }}</td>
                            <td>{{ $doctor->specialization ?? 'General' }}</td>
                            <td>
                                @if($doctor->facility)
                                    {{ $doctor->facility->name }} <br>
                                    <small class="text-muted">{{ $doctor->facility->type }}</small>
                                @else
                                    <span class="text-muted small">Not Assigned</span>
                                @endif
                            </td>
                            <td><span class="badge badge-primary">{{ $doctor->patients_count }}</span></td>
                            <td><span class="badge badge-success">{{ $doctor->prescriptions_count }}</span></td>
                            <td>{{ $doctor->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this doctor? This may affect associated data.');">
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
                            <td colspan="8" class="text-center py-4">No doctors registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $doctors->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
