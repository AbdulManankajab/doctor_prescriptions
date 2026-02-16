@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('header-actions')
<a href="{{ route('admin.export.anonymized') }}" class="btn btn-primary">
    <i class="fas fa-file-export mr-1"></i> Export Anonymized Data (MoH)
</a>
@endsection

@section('content')
<div class="row">
    <!-- Total Patients -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalPatients }}</h3>
                <p>Total Patients</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.patients.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Prescriptions -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPrescriptions }}</h3>
                <p>Total Prescriptions</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-prescription"></i>
            </div>
            <a href="{{ route('admin.prescriptions.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Today's Prescriptions -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $recentPrescriptions->where('created_at', '>=', now()->startOfDay())->count() }}</h3>
                <p>Today's Prescriptions</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <a href="{{ route('admin.prescriptions.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Examinations -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-maroon" style="background-color: #d81b60 !important; color: white;">
            <div class="inner">
                <h3>{{ $totalExaminations }}</h3>
                <p>Total Examinations</p>
            </div>
            <div class="icon">
                <i class="fas fa-microscope" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="#" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Doctors -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple" style="background-color: #6f42c1 !important; color: white;">
            <div class="inner">
                <h3>{{ $totalDoctors }}</h3>
                <p>Total Doctors</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-md" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="{{ route('admin.doctors.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Total Pharmacy Users -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-teal" style="background-color: #20c997 !important; color: white;">
            <div class="inner">
                <h3>{{ $totalPharmacyUsers }}</h3>
                <p>Pharmacy Users</p>
            </div>
            <div class="icon">
                <i class="fas fa-capsules" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="{{ route('admin.pharmacy.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Pending/Ready to Dispense -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-orange" style="background-color: #fd7e14 !important; color: white;">
            <div class="inner">
                <h3>{{ $pendingPrescriptions }}</h3>
                <p>Ready to Dispense</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="{{ route('admin.prescriptions.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Dispensed -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-olive" style="background-color: #3d9970 !important; color: white;">
            <div class="inner">
                <h3>{{ $dispensedPrescriptions }}</h3>
                <p>Dispensed Prescriptions</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-double" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="{{ route('admin.prescriptions.index') }}" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    <!-- Total Visits -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-indigo" style="background-color: #6610f2 !important; color: white;">
            <div class="inner">
                <h3>{{ $totalVisits }}</h3>
                <p>Total Patient Visits</p>
            </div>
            <div class="icon">
                <i class="fas fa-walking" style="color: rgba(255,255,255,0.3)"></i>
            </div>
            <a href="#" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Diagnoses -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i> Top Diagnoses Trends
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($topDiagnoses as $diag)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $diag->primary_diagnosis }}
                        <span class="badge badge-primary badge-pill">{{ $diag->count }} cases</span>
                    </li>
                    @endforeach
                    @if($topDiagnoses->isEmpty())
                        <li class="list-group-item text-center text-muted">No diagnosis data yet</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Quick Analytics Info -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i> Medical Data Compliance
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    This system is designed to comply with medical data privacy standards. 
                    Anonymized exports remove patient IDs and names while retaining clinical data for public health reporting.
                </p>
                <div class="alert alert-light border">
                    <i class="fas fa-shield-alt text-success mr-2"></i> All data is encrypted and stored securely.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Prescriptions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i> Recent Prescriptions
                </h3>
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
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPrescriptions as $prescription)
                        <tr>
                            <td><strong>{{ $prescription->prescription_number }}</strong></td>
                            <td>{{ $prescription->patient->name }}</td>
                            <td><span class="badge badge-info">{{ $prescription->doctor->name ?? 'System' }}</span></td>
                            <td>{{ Str::limit($prescription->diagnosis, 40) }}</td>
                            <td>
                                @if($prescription->status === 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($prescription->status === 'sent')
                                    <span class="badge badge-warning">Sent</span>
                                @elseif($prescription->status === 'dispensed')
                                    <span class="badge badge-success">Dispensed</span>
                                @endif
                            </td>
                            <td>{{ $prescription->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" 
                                   class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.prescriptions.print', $prescription->id) }}" 
                                   class="btn btn-xs btn-primary" 
                                   target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No prescriptions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
