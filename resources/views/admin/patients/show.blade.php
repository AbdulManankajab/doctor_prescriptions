@extends('admin.layouts.admin')

@section('title', 'Patient Details')
@section('page-title', 'Patient Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item active">{{ $patient->name }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Patient Info -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>
                <h3 class="profile-username text-center">{{ $patient->name }}</h3>
                <p class="text-muted text-center">{{ $patient->patient_number }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Age</b> <span class="float-right">{{ $patient->age }} years</span>
                    </li>
                    <li class="list-group-item">
                        <b>Gender</b> <span class="float-right">{{ $patient->gender }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b> <span class="float-right">{{ $patient->phone }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Registered</b> <span class="float-right">{{ $patient->created_at->format('d M Y') }}</span>
                    </li>
                </ul>

                @if($patient->address)
                <p><strong>Address:</strong><br>{{ $patient->address }}</p>
                @endif

                <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit Patient
                </a>
                <a href="{{ route('prescription.create', $patient->id) }}" target="_blank" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> New Prescription
                </a>
            </div>
        </div>
    </div>

    <!-- Prescription History -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-prescription"></i> Prescription History ({{ $patient->prescriptions->count() }})
                </h3>
            </div>
            <div class="card-body">
                @forelse($patient->prescriptions->sortByDesc('created_at') as $prescription)
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <strong>{{ $prescription->prescription_number }}</strong>
                        </h5>
                        <div class="card-tools">
                            <span class="badge badge-info">{{ $prescription->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Diagnosis:</strong> {{ $prescription->diagnosis }}</p>
                        
                        @if($prescription->notes)
                        <p><strong>Notes:</strong> {{ $prescription->notes }}</p>
                        @endif

                        <h6 class="mt-3">Medicines:</h6>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Medicine</th>
                                    <th width="10%">Type</th>
                                    <th width="15%">Dosage</th>
                                    <th width="15%">Duration</th>
                                    <th width="30%">Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->medicine->name }}</td>
                                    <td>{{ ucfirst($item->type) }}</td>
                                    <td>{{ $item->dosage }}</td>
                                    <td>{{ $item->duration }}</td>
                                    <td>{{ $item->instructions ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-2">
                            <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('prescription.print', $prescription->id) }}" 
                               class="btn btn-sm btn-primary" 
                               target="_blank">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">
                    No prescriptions found for this patient.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
