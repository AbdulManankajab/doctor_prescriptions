@extends('admin.layouts.admin')

@section('title', 'Patients')
@section('page-title', 'Patients Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Patients</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">All Patients</h3>
            </div>
            <div class="card-body bg-light border-bottom">
                <form action="{{ route('admin.patients.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="doctor_id" class="form-control" onchange="this.form.submit()">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Patient No</th>
                            <th>Name</th>
                            <th>Doctor</th>
                            <th>Age/Gender</th>
                            <th>Phone</th>
                            <th>Prescriptions</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td>{{ $patient->patient_number }}</td>
                            <td>{{ $patient->name }}</td>
                            <td>
                                <span class="badge badge-info">{{ $patient->doctor->name ?? 'System' }}</span>
                            </td>
                            <td>{{ $patient->age }} / {{ $patient->gender }}</td>
                            <td>{{ $patient->phone }}</td>
                            <td>
                                <span class="badge badge-info">{{ $patient->prescriptions_count }}</span>
                            </td>
                            <td>{{ $patient->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.patients.show', $patient->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.patients.edit', $patient->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.patients.destroy', $patient->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this patient?');">
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
                            <td colspan="7" class="text-center">No patients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $patients->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
