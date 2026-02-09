@extends('admin.layouts.admin')

@section('title', 'Prescriptions')
@section('page-title', 'Prescriptions Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Prescriptions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">All Prescriptions</h3>
            </div>
            <div class="card-body bg-light border-bottom">
                <form action="{{ route('admin.prescriptions.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="doctor_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-default">Reset</a>
                    </div>
                    <div class="col-md-2 ml-auto">
                        <a href="{{ route('scan.index') }}" class="btn btn-info">
                            <i class="fas fa-qrcode mr-1"></i> Scan QR
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Prescription No</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Diagnosis</th>
                            <th>Status</th>
                            <th>Medicines</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->prescription_number }}</td>
                            <td>
                                {{ $prescription->patient->name }}
                                <br>
                                <small class="text-muted">{{ $prescription->patient->patient_number }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $prescription->doctor->name ?? 'System' }}</span>
                                <br>
                                <small class="text-xs">{{ $prescription->doctor->specialization ?? '' }}</small>
                            </td>
                            <td>{{ Str::limit($prescription->diagnosis, 30) }}</td>
                            <td>
                                @if($prescription->status === 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($prescription->status === 'sent')
                                    <span class="badge badge-warning">Sent to Pharmacy</span>
                                @elseif($prescription->status === 'dispensed')
                                    <span class="badge badge-success">Dispensed</span>
                                @else
                                    <span class="badge badge-light">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $prescription->items->count() }}</span>
                            </td>
                            <td>{{ $prescription->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.prescriptions.print', $prescription->id) }}" 
                                   class="btn btn-sm btn-primary" 
                                   target="_blank" 
                                   title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                                <form action="{{ route('admin.prescriptions.destroy', $prescription->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this prescription?');">
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
                            <td colspan="6" class="text-center">No prescriptions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $prescriptions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
