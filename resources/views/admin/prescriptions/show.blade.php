@extends('admin.layouts.admin')

@section('title', 'Prescription Details')
@section('page-title', 'Prescription Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.prescriptions.index') }}">Prescriptions</a></li>
    <li class="breadcrumb-item active">{{ $prescription->prescription_number }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-prescription"></i> {{ $prescription->prescription_number }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $prescription->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Patient Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user"></i> Patient Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Name:</th>
                                <td>
                                    <a href="{{ route('admin.patients.show', $prescription->patient->id) }}">
                                        {{ $prescription->patient->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Patient Number:</th>
                                <td>{{ $prescription->patient->patient_number }}</td>
                            </tr>
                            <tr>
                                <th>Age/Gender:</th>
                                <td>{{ $prescription->patient->age }} / {{ $prescription->patient->gender }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $prescription->patient->phone }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-clipboard"></i> Prescription Info</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Prescription No:</th>
                                <td>{{ $prescription->prescription_number }}</td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>{{ $prescription->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Total Medicines:</th>
                                <td><span class="badge badge-success">{{ $prescription->items->count() }}</span></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
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
                            </tr>
                            @if($prescription->status === 'dispensed')
                            <tr>
                                <th>Dispensed At:</th>
                                <td>{{ $prescription->dispensed_at ? $prescription->dispensed_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Dispensed By:</th>
                                <td>{{ $prescription->dispensedBy->name ?? 'Pharmacy' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @if($prescription->qr_token)
                    <div class="col-md-2 text-center d-flex flex-column align-items-center justify-content-center">
                        <div class="p-2 border rounded bg-white shadow-sm">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($prescription->qr_token) !!}
                        </div>
                        <div class="mt-2 small text-muted font-weight-bold">SECURE QR TOKEN</div>
                        <div class="text-xs text-muted">{{ $prescription->qr_token }}</div>
                    </div>
                    @endif
                </div>

                <!-- Diagnosis -->
                <div class="mb-4">
                    <h5><i class="fas fa-stethoscope"></i> Diagnosis</h5>
                    <div class="border rounded p-3 bg-light">
                        {{ $prescription->diagnosis }}
                    </div>
                </div>

                <!-- Notes -->
                @if($prescription->notes)
                <div class="mb-4">
                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                    <div class="border rounded p-3 bg-light">
                        {{ $prescription->notes }}
                    </div>
                </div>
                @endif

                <!-- Medicines -->
                <div class="mb-4">
                    <h5><i class="fas fa-pills"></i> Prescribed Medicines</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Medicine Name</th>
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
                                <td><strong>{{ $item->medicine->name }}</strong></td>
                                <td><span class="badge badge-info">{{ ucfirst($item->type) }}</span></td>
                                <td>{{ $item->dosage }}</td>
                                <td>{{ $item->duration }}</td>
                                <td>{{ $item->instructions ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="text-right">
                    <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('prescription.print', $prescription->id) }}" 
                       target="_blank" 
                       class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Prescription
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
