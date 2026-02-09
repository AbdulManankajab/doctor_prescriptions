@extends('layouts.doctor')

@section('title', 'Patient History')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white p-4 border-0">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history text-primary me-2"></i> Patient Prescription History
                </h3>
            </div>
            <div class="card-body p-4">
                <!-- Patient Info -->
                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="text-muted small text-uppercase fw-bold mb-3">Personal Details</h6>
                            <h4 class="mb-1 fw-bold text-primary">{{ $patient->name }}</h4>
                            <p class="mb-1"><strong>Patient ID:</strong> {{ $patient->patient_number }}</p>
                            <p class="mb-0"><strong>Age/Gender:</strong> {{ $patient->age }} years / {{ $patient->gender }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="text-muted small text-uppercase fw-bold mb-3">Contact Information</h6>
                            <p class="mb-1"><strong>Phone:</strong> {{ $patient->phone }}</p>
                            <p class="mb-0"><strong>Address:</strong> {{ $patient->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <h5 class="mb-4 fw-bold">Recent Visits ({{ $patient->prescriptions->count() }})</h5>
                
                @if($patient->prescriptions->count() > 0)
                    <div class="accordion accordion-flush" id="historyAccordion">
                        @foreach($patient->prescriptions as $index => $prescription)
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} bg-white py-3 px-4" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#visit{{ $prescription->id }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                        <div>
                                            <span class="fw-bold fs-5 text-dark">{{ $prescription->prescription_number }}</span>
                                            <span class="ms-3 text-muted small"><i class="bi bi-calendar3 me-1"></i> {{ $prescription->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @if($prescription->status === 'draft')
                                                <span class="badge bg-secondary me-2 rounded-pill">Draft</span>
                                            @elseif($prescription->status === 'sent')
                                                <span class="badge bg-warning text-dark me-2 rounded-pill">Sent</span>
                                            @elseif($prescription->status === 'dispensed')
                                                <span class="badge bg-success me-2 rounded-pill">Dispensed</span>
                                            @endif
                                            <span class="badge bg-primary rounded-pill px-3">{{ $prescription->items->count() }} Meds</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="visit{{ $prescription->id }}" 
                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                 data-bs-parent="#historyAccordion">
                                <div class="accordion-body p-4 bg-white border-top">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h6 class="fw-bold text-primary mb-2">Diagnosis:</h6>
                                            <p class="bg-light p-3 rounded-3 mb-3">{{ $prescription->diagnosis }}</p>
                                            
                                            @if($prescription->notes)
                                                <h6 class="fw-bold text-primary mb-2">Advice & Notes:</h6>
                                                <p class="bg-light p-3 rounded-3" style="white-space: pre-line;">{{ $prescription->notes }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mb-3">Medications List:</h6>
                                    <div class="table-responsive rounded-3">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Medicine</th>
                                                    <th>Type</th>
                                                    <th>Dosage</th>
                                                    <th>Duration</th>
                                                    <th>Timing</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($prescription->items as $item)
                                                <tr>
                                                    <td><strong>{{ $item->medicine->name }}</strong></td>
                                                    <td>{{ ucfirst($item->type) }}</td>
                                                    <td>{{ $item->dosage }}</td>
                                                    <td>{{ $item->duration }}</td>
                                                    <td>{{ $item->instructions }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end mt-4">
                                        @if($prescription->qr_token)
                                            <div class="text-center p-2 border rounded bg-white">
                                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($prescription->qr_token) !!}
                                                <div style="font-size: 9px; margin-top: 5px; color: #888;">SECURE QR RECORD</div>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}" 
                                               class="btn btn-outline-primary px-4 rounded-pill" 
                                               target="_blank">
                                                <i class="bi bi-printer me-2"></i> Print This Version
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info rounded-3 border-0 shadow-sm">
                        <i class="bi bi-info-circle me-2"></i> No prescription history available for this patient.
                    </div>
                @endif

                <div class="mt-5 pt-4 border-top d-flex justify-content-between">
                    <a href="{{ route('doctor.dashboard') }}" class="btn btn-light px-4 rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('doctor.prescription.create', $patient->id) }}" class="btn btn-primary px-4 rounded-pill">
                        <i class="bi bi-plus-circle me-2"></i> New Prescription for {{ $patient->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
